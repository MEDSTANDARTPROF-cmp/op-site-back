<?php

interface MsIeWatcherInterface
{


    public function run();

    /**
     * @return int
     */
    public function getCount();

    /**
     * @return array
     */
    public function getStates();

    /**
     * @param int $pid
     * @return bool
     */
    public function remove($pid);

    /**
     * @param MsieTask $task
     * @return bool
     */
    public function addKillTask(MsieTask $task);

}

class MsIeWatcher implements MsIeWatcherInterface
{
    const STATUS_RUNNING = 'running';
    const STATUS_REMOVE = 'remove';

    /** @var modX $modx */
    public $modx;
    /** @var Msie $msie */
    public $msie;
    /** @var MsIeTools $tools */
    public $tools;
    /** @var int $pid */
    protected $pid;
    /** @var int $time */
    protected $time = 0;
    /** @var bool */
    protected $debug = false;
    /** @var bool */
    protected $loop = true;
    /** @var bool $daemonMode */
    protected $daemonMode = false;
    /** @var int $lifetime */
    protected $lifetime;
    /** @var int $maxExecutionTime */
    protected $maxExecutionTime;
    /** @var  int $watcherMaxCount */
    protected $watcherMaxCount;
    /** @var int $reconnectTimeout */
    protected $reconnectTimeout = 28800;
    /** @var bool $isMaster */
    protected $isMaster = false;

    /**
     * MsIeWatcher constructor.
     * @param Msie $msie
     */
    public function __construct(Msie &$msie)
    {
        $this->msie = &$msie;
        $this->modx = $this->msie->modx;
        $this->tools = $this->msie->getTools();
        $this->debug = $this->tools->getSysSetting('watcher_debug', false);
    }

    public function run()
    {
        $this->debug('Run watcher');
        $this->time = time();
        $this->pid = getmypid();
        $countWatchers = $this->getCount();
        $this->watcherMaxCount = $this->tools->getSysSetting('watcher_max_count', 1);
        $this->modx->call('MsieCron', 'revise', array(&$this->msie));
        $this->gc();
        $this->killTasks();
        $this->changeStatusTasksKilledSystem();

        if ($this->watcherMaxCount <= $countWatchers) {
            $this->debug("Run watcher stopped.\n watcherMaxCount: {$this->watcherMaxCount}\n countWatchers: {$countWatchers}");
            return;
        }

        $this->lifetime = $this->tools->getSysSetting('watcher_lifetime', 1800);
        $this->daemonMode = $this->tools->checkDaemonMode();
        $wait = $this->tools->getSysSetting('watcher_wait', 3);
        $scriptTimeLimit = $this->tools->getSysSetting('script_time_limit', 0);

        register_shutdown_function(array(&$this, 'shutdown'));

        if ($scriptTimeLimit) {
            @set_time_limit($scriptTimeLimit);
        }

        $this->maxExecutionTime = ini_get('max_execution_time');

        if ($this->maxExecutionTime) {
            $this->reconnectTimeout = $this->maxExecutionTime - 60;
            if (!$this->daemonMode && $this->maxExecutionTime > 60) {
                $this->maxExecutionTime = $this->maxExecutionTime - $this->maxExecutionTime / 3;
            }
        }

        $this->debug("Start work watcher with params:\n pid: {$this->pid}\n time: {$this->time}\n daemonMode: {$this->daemonMode}\n sleepTime: {$wait}\n scriptTimeLimit: {$scriptTimeLimit}\n reconnectTimeout: {$this->reconnectTimeout}\n maxExecutionTime: {$this->maxExecutionTime}");

        if (!$this->register()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error register watcher');
            return;
        }

        MsIeTools::setSessionWaitTimeout($this->modx, $this->reconnectTimeout);

        while ($this->loop) {
            $this->process();
            if ($wait) {
                sleep($wait);
            } else {
                $this->loop = false;
            }
        }
    }

    /**
     * @param bool $revise
     * @return int
     */
    public function getCount($revise = true)
    {
        return count($this->loadStates($revise));
    }

    /**
     * @param bool $revise
     * @return array
     */
    public function getStates($revise = true)
    {
        return $this->loadStates($revise);
    }

    /**
     * @param MsieTask $task
     * @return bool
     */
    public function addKillTask(MsieTask $task)
    {
        $result = false;
        $tasks = $this->loadKillTasks();
        $tasks[$task->get('id')] = $task->get('pid');
        if ($this->saveKillTasks($tasks)) {
            $result = $task->changeStatus(MsieTask::STATUS_WAITING_KILL);
        }
        return $result;
    }

    /**
     * @param int $pid
     * @return bool
     */
    public function remove($pid)
    {
        $state = $this->getState($pid);
        if ($state) {
            $state['status'] = self::STATUS_REMOVE;
            return $this->setState($pid, $state);
        } else {
            return $this->unregister($pid, true);
        }
    }

    /**
     * @param int $pid
     * @return bool
     */
    public function isRunning($pid = 0)
    {
        $pid = $pid ? $pid : $this->pid;
        try {
            exec("ps -p {$pid}", $result);
            if (count($result) >= 2) {
                return true;
            }
        } catch (Exception $e) {
        }
        return false;
    }

    public function shutdown()
    {
        $this->unregister();
        if ($err = error_get_last()) {
            $this->debug($err);
        }
    }

    /**
     * @return bool
     */
    protected function register()
    {
        return $this->addState(array(
            'pid' => $this->pid,
            'time' => time(),
            'status' => self::STATUS_RUNNING,
            'max_execution_time' => $this->maxExecutionTime,
        ));
    }

    /**
     * @param int $pid
     * @param bool $kill
     * @return bool
     */
    protected function unregister($pid = 0, $kill = false)
    {
        $pid = $pid ? $pid : $this->pid;
        if ($kill && $this->isRunning($pid)) {
            $this->kill($pid);
        }
        return $this->removeState($pid);
    }

    protected function killTasks()
    {
        $tasks = $this->loadKillTasks(false);
        if ($tasks) {
            foreach ($tasks as $id => $pid) {
                if ($this->kill($pid)) {
                    $task = $this->modx->getObject('MsieTask', $id);
                    /** @var MsieTask $task */
                    if ($task) {
                        if ($task->changeStatus(MsieTask::STATUS_KILLED)) {
                            unset($tasks[$id]);
                        } else {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error save change status to kill for task id: {$id}");
                        }
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Error kill task id: {$id}; pid: {$pid}");
                }
            }
            $this->saveKillTasks($tasks);
        }
    }

    /**
     * @param int $pid
     * @return bool
     */
    protected function kill($pid = 0)
    {
        $pid = $pid ?: $this->pid;
        if ($this->tools->checkExec()) {
            exec('kill -9 ' . $pid);
            return !$this->isRunning($pid);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error kill watcher pid: {$pid}. Function exec disabled");
        }
        return false;
    }


    protected function process()
    {
        $this->quit();
        $this->reconnect();
        if ($this->isMaster()) {
            if ($taskId = $this->getTaskIdToRun()) {
                if (!$this->daemonMode) {
                    $this->reconnect(true);
                    $this->debug("Unregister watcher pid: {$this->pid} before run task: {$taskId}");
                    $this->unregister();
                }
                $this->debug("Watcher pid: {$this->pid} run task: {$taskId}");
                $this->msie->getTaskManager()->run($taskId);

                if (!$this->daemonMode) {
                    $this->debug("Stop watcher pid: {$this->pid} after work task: {$taskId}");
                    $this->quit(true);
                }
            }
        }
    }

    /**
     * @return int
     */
    protected function getTaskIdToRun()
    {
        $id = 0;
        $table = $this->modx->getTableName('MsieTask');
        $sql = "SELECT `id` FROM {$table} WHERE `status` IN ('initiated','waiting') ORDER BY `id` ASC LIMIT 1;";
        $q = $this->modx->prepare($sql);
        if ($q->execute()) {
            $id = $q->fetch(PDO::FETCH_COLUMN);
        }
        return $id;
    }

    protected function changeStatusTasksKilledSystem()
    {
        $tasks = $this->getRunningTasksPid();
        if (!$tasks) {
            return;
        }

        $ids = [];
        $pids = $this->findWatchersPid();
        foreach ($tasks as $id => $pid) {
            if (!in_array($pid, $pids)) {
                $ids[] = $id;
            }
        }

        if (!$ids) {
            return;
        }

        $ids = implode(',', $ids);
        $status = MsieTask::STATUS_KILLED;
        $table = $this->modx->getTableName('MsieTask');
        $sql = "UPDATE {$table} SET `status` = '{$status}' WHERE `id` IN ({$ids});";
        $this->tools->execSql($sql);
    }

    protected function getRunningTasksPid()
    {
        $result = array();
        $classKey = 'MsieTask';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', ['id', 'pid']));

        $q->where(array(
            "`{$classKey}`.`status`:=" => MsieTask::STATUS_RUNNING
        ));

        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$row['id']] = $row['pid'];
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isMaster()
    {
        if (!$this->isMaster) {
            $this->isMaster = true;
            if ($this->watcherMaxCount > 1) {
                if ($states = $this->loadStates()) {
                    $data = array_shift($states);
                    $this->isMaster = $data['pid'] == $this->pid;
                }
            }
        }
        return $this->isMaster;
    }

    protected function gc()
    {
        if ($gcTimeRun = $this->tools->getSysSetting('gc_schedule', 0)) {
            $schedule = Cron\CronExpression::factory($gcTimeRun);
            if ($schedule->isDue()) {
                $this->debug("Watcher pid: {$this->pid} run garbage collector. gcTimeRun: {$gcTimeRun}");
                $this->tools->gc();
                $this->msie->getTaskManager()->gc();
            }
        }
    }

    /**
     * @param bool $force
     */
    protected function reconnect($force = false)
    {
        $time = time() - $this->time;
        if ($time >= $this->reconnectTimeout || $force) {
            $this->debug("Reconnect watcher pid: {$this->pid}\n time: {$time}\n reconnectTimeout: {$this->reconnectTimeout}\n force: {$force}");
            MsIeTools::reconnect($this->modx, $this->reconnectTimeout);
        }


    }

    /**
     * @param bool $force
     */
    protected function quit($force = false)
    {
        $time = time() - $this->time;
        if (
            $time > $this->lifetime ||
            $force
        ) {
            $this->debug("Watcher pid: {$this->pid} quit.\n time:{$time} maxExecutionTime:\n {$this->maxExecutionTime}\n force: {$force}");
            exit();
        }
    }

    /**
     * @return array
     */
    protected function findWatchersPid()
    {
        $script = $this->msie->config['watcherScriptPath'];
        exec("pgrep -f '.*{$script}'", $result);
        return $result ?: array();

    }

    /**
     * @param int $pid
     * @return array
     */
    protected function getState($pid)
    {
        $states = $this->loadStates();
        return isset($states[$pid]) ? $states[$pid] : array();
    }

    /**
     * @param int $pid
     * @param array $state
     * @return bool
     */
    protected function setState($pid, array $state)
    {
        $states = $this->loadStates();
        $states[$pid] = $state;
        return $this->saveStates($states);
    }

    /**
     * @param array $state
     * @return bool
     */
    protected function addState(array $state)
    {
        $states = $this->loadStates();
        $states[$this->pid] = $state;
        return $this->saveStates($states);
    }


    /**
     * @param $key
     * @return bool
     */
    protected function removeState($key)
    {
        if ($states = $this->loadStates(false)) {
            unset($states[$key]);
        }
        return $this->saveStates($states);
    }

    /**
     * @param bool $revise
     * @return array
     */
    protected function loadStates(bool $revise = true)
    {
        $states = array();
        $file = $this->getStatesFile();
        if (file_exists($file)) {
            if (is_readable($file)) {
                $content = '';
                if ($handle = fopen($file, 'r')) {
                    if (flock($handle, LOCK_SH)) {
                        while (!feof($handle)) {
                            $content .= fread($handle, 8192);
                        }
                        flock($handle, LOCK_UN);
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Unable to get a lock on file: {$file}");
                    }
                    fclose($handle);
                    if ($data = $this->tools->fromJSON($content, array())) {
                        if ($revise) {
                            if ($pids = $this->findWatchersPid()) {
                                foreach ($pids as $pid) {
                                    if (isset($data[$pid])) {
                                        if ($data[$pid]['status'] == self::STATUS_RUNNING) {
                                            $states[$pid] = $data[$pid];
                                        } else if ($data[$pid]['status'] == self::STATUS_REMOVE) {
                                            $this->unregister($pid, true);
                                        }
                                    }
                                }
                            }
                        } else {
                            $states = $data;
                        }
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not retrieve content from file '{$file}'.");
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "File {$file} is not readable");
            }
        }
        return $states;
    }


    /**
     * @return string
     */
    protected function getStatesFile()
    {
        $path = $this->msie->config['dataPath'] . 'watcher/';
        if (!file_exists($path)) {
            $this->modx->cacheManager->writeTree($path);
        }
        return $path . 'states.var';
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    protected function saveStates(array $data)
    {
        $file = $this->getStatesFile();
        return $this->saveVarData($data, $file);
    }


    /**
     * @param array $data
     *
     * @return bool
     */
    protected function saveKillTasks(array $data)
    {
        $file = $this->getKillTasksFile();
        return $this->saveVarData($data, $file);
    }


    /**
     * @param bool $revise
     * @return array
     */
    protected function loadKillTasks(bool $revise = true)
    {
        $result = array();
        $file = $this->getKillTasksFile();
        if (file_exists($file)) {
            if (is_readable($file)) {
                $content = '';
                if ($handle = fopen($file, 'r')) {
                    if (flock($handle, LOCK_SH)) {
                        while (!feof($handle)) {
                            $content .= fread($handle, 8192);
                        }
                        flock($handle, LOCK_UN);
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Unable to get a lock on file: {$file}");
                    }
                    fclose($handle);
                    if ($data = $this->tools->fromJSON($content, array())) {
                        if ($revise) {
                            if ($pids = $this->findWatchersPid()) {
                                foreach ($pids as $pid) {
                                    $id = array_search($pid, $data);
                                    if ($id !== false) {
                                        $result[$id] = $pid;
                                    }
                                }
                            }
                        } else {
                            $result = $data;
                        }
                    }
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not retrieve content from file '{$file}'.");
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "File {$file} is not readable");
            }
        }
        return $result;
    }


    /**
     * @return string
     */
    protected function getKillTasksFile()
    {
        $path = $this->msie->config['dataPath'] . 'watcher/';
        if (!file_exists($path)) {
            $this->modx->cacheManager->writeTree($path);
        }
        return $path . 'kill.var';
    }

    /**
     * @param array $data
     * @param string $file
     * @param int|string $fileMode
     *
     * @return bool
     */
    protected function saveVarData(array $data, string $file, $fileMode = 0777)
    {
        $result = false;
        if (is_string($fileMode)) $fileMode = octdec($fileMode);
        $content = $this->modx->toJSON($data);
        if ($handle = fopen($file, 'c')) {
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                ftruncate($handle, 0);
                if (fwrite($handle, $content) !== false) {
                    fflush($handle);
                    flock($handle, LOCK_UN);
                    $result = true;
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Unable to get a lock on file: {$file}");
            }
            fclose($handle);
            @chmod($file, $fileMode);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Could not write content from file '{$file}'.");
        }

        return $result;
    }


    /**
     * @param string $message
     */
    protected function debug($message)
    {
        if (!$this->debug) return;
        $this->tools->debug($message);
    }

}