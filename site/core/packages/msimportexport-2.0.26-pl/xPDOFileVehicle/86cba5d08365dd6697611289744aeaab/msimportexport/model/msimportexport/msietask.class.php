<?php
/**
 * @property MsiePreset $Preset
 * @see MsiePreset
 * @package msimportexport
 */

class MsieTask extends xPDOSimpleObject
{
    const STATUS_INITIATED = 'initiated'; // creating Queued Pending  Processing Assigned
    const STATUS_RUNNING = 'running';
    const STATUS_WAITING = 'waiting';
    const STATUS_STOPPED = 'stopped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_WAITING_KILL = 'waiting_kill';
    const STATUS_KILLED = 'killed';

    /** @var Msie $msie */
    public $msie;
    /** @var array $config */
    protected $config = array();
    /** @var int $autoRestartLimit */
    protected $autoRestartLimit = 7;
    /** @var null|string $runner */
    protected $runner;
    /** @var int $sessionWaitTimeout */
    protected $sessionWaitTimeout = 3600;
    /** @var null|string $binary */
    protected $binary;
    /** @var MsiePreset $preset */
    protected $preset = null;
    /** @var MsIeService $service */
    protected $service = null;
    /** @var MsIeWorker $worker */
    protected $worker = null;
    /** @var bool $daemonMode */
    protected $daemonMode = false;

    public function __construct(xPDO $xpdo)
    {
        parent::__construct($xpdo);
        $this->msie = $this->xpdo->getService('msimportexport', 'Msie');
        $this->config = array_merge(array(), $this->msie->config);
        $this->daemonMode = $this->msie->getTools()->checkDaemonMode();
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return array(
            'INITIATED' => self::STATUS_INITIATED,
            'RUNNING' => self::STATUS_RUNNING,
            'WAITING' => self::STATUS_WAITING,
            'STOPPED' => self::STATUS_STOPPED,
            'COMPLETED' => self::STATUS_COMPLETED,
            'FAILED' => self::STATUS_FAILED,
            'KILLED' => self::STATUS_KILLED,
        );
    }

    /**
     * @return MsiePreset|null
     */
    public function getPreset()
    {
        if (!$this->preset) {
            $this->preset = $this->xpdo->getObject('MsiePreset', $this->get('preset_id'));
        }
        return $this->preset;
    }

    /**
     * @return MsieService|null
     * @throws Exception
     */
    public function getService()
    {
        if (!$this->service) {
            if ($this->getPreset()) {
                $mode = $this->getMode();
                $service = $this->getPreset()->get('service');
                $service = $this->getSetting('service', $service);
                $this->service = $this->msie->getService($mode, $service);
            }
        }
        return $this->service;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->getPreset()->get('mode');
    }

    /**
     * @param array $properties
     * @return MsIeWorker|null
     * @throws Exception
     */
    public function getWorker(array $properties = array())
    {
        if (!$this->worker) {
            if ($this->getService()) {
                $this->worker = $this->service->getWorker($properties);
            }
        }
        return $this->worker;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        $settings = array_merge(
            $this->msie->getTools()->getSysSettings(),
            $this->getPreset()->getSettings(),
            $this->get('settings')

        );
        $settings['tmp_path'] = $this->msie->getTools()->preparePath($settings['tmp_path']);
        $settings['upload_path'] = $this->msie->getTools()->preparePath($settings['upload_path']);
        return $settings ?? array();
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getSetting($key, $default = null)
    {
        $settings = $this->getSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;

    }


    /**
     * @return bool|int
     * @throws Exception
     */
    public function run()
    {


        if ($this->get('pid')) {
            if ($this->isRunning()) return true;
            if (in_array($this->get('status'), array(self::STATUS_COMPLETED, self::STATUS_KILLED))) {
                return false;
            } else if ($this->get('status') === self::STATUS_FAILED) {
                $this->set('restarted', 0);
            }
        }

        if (!$this->getPreset()) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Error get preset for task ID:' . $this->get('id'));
            $this->changeStatus(self::STATUS_FAILED);
            return false;
        }

        if (!$this->getWorker($this->get('properties'))) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Error get worker for task ID:' . $this->get('id') . ' settings:' . print_r($this->get('properties'), 1));
            $this->changeStatus(self::STATUS_FAILED);
            return false;
        }

        $state = $this->getState();

        if (!is_array($state)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Error! State data is not valid. Task ID:' . $this->get('id'));
            $this->changeStatus(self::STATUS_FAILED);
            return false;
        }

        $pid = getmypid();
        $this->set('pid', $pid);
        $this->set('start_time', microtime(true));
        if (!$this->changeStatus(self::STATUS_RUNNING)) return false;

        register_shutdown_function(array($this, 'shutdown'));

        $settings = $this->getSettings();
        $timeLimit = $this->xpdo->getOption('script_time_limit', $settings, 0);
        $memoryLimit = $this->xpdo->getOption('script_memory_limit', $settings, 0);
        $this->sessionWaitTimeout = $this->xpdo->getOption('mysql_wait_timeout', $settings, 3600);
        $this->autoRestartLimit = $this->xpdo->getOption('auto_restart_limit', $settings, 7);

        if ($timeLimit) {
            set_time_limit($timeLimit);
        }

        if ($memoryLimit) {
            ini_set('memory_limit', $memoryLimit . 'M');
        }

        if ($this->daemonMode && $this->sessionWaitTimeout) {
            $this->xpdo->exec("set session wait_timeout={$this->sessionWaitTimeout}; set session interactive_timeout={$this->sessionWaitTimeout};");
        }


        $this->worker->setTask($this);
        $this->worker->setFields($this->getPreset()->getFields());
        $this->worker->setSettings($settings);
        $this->worker->restoreState($state);

        $this->worker->run();

        if ($this->worker->isStop()) {
            return $this->changeStatus(self::STATUS_STOPPED, true);
        } else {
            $this->set('finish_time', microtime(true));
            return $this->changeStatus(self::STATUS_COMPLETED, true);
        }
    }


    /**
     * @return bool
     */
    public function isRunning()
    {
        try {
            exec("ps -p {$this->get('pid')}", $result);
            if (count($result) >= 2) {
                return true;
            }
        } catch (Exception $e) {
        }
        return false;
    }

    public function stop($cli = false)
    {
        if (!$this->isCli() || $cli) return $this->call(__FUNCTION__);

        if (
            $this->isRunning() == false &&
            $this->get('status') === self::STATUS_RUNNING
        ) {
            return $this->changeStatus(self::STATUS_STOPPED);
        }

        $stdout = $this->getStdOut();
        $stderr = $this->getStdErr();
        if (function_exists('pcntl_signal')) {
            $this->exec("kill -15 %s {$stdout} {$stderr}", array($this->get('pid'))); // SIGTERM
        } else {
            $this->kill();
        }
    }

    /**
     * @return bool
     */
    public function kill()
    {
        $stdout = $this->getStdOut();
        $stderr = $this->getStdErr();
        $this->exec("kill -9 %s {$stdout} {$stderr}", array($this->get('pid'))); // SIGKILL
        if (
            $this->isRunning() == false &&
            $this->get('status') === self::STATUS_RUNNING
        ) {

            return $this->changeStatus(self::STATUS_KILLED);
        }
        return false;
    }

    /**
     * @param null $cacheFlag
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $isNew = $this->isNew();
        if ($isNew) {
            $token = $this->generateToken();
            $this->set('token', $token);
        }
        if ($saved = parent::save($cacheFlag)) {
            if ($isNew) {
                $this->sendNotice();
            }
        }
        return $saved;
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        $token = uniqid('task_' . rand(), true);
        return md5($token);
    }


    public function remove(array $ancestors = array())
    {
        /** @var  MsiePreset $preset */
        $preset = $this->getOne('Preset');
        if ($preset && $preset->get('mode') == Msie::MODE_EXPORT) {
            if ($report = $this->getReport(true)) {
                $file = $this->xpdo->getOption('file', $report);
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
        if ($this->isRunning()) $this->kill();
        if ($ok = parent::remove($ancestors)) {
            $this->xpdo->getCacheManager()->deleteTree($this->getPath(), array('deleteTop' => true, 'extensions' => ''));

        }
        return $ok;
    }

    /**
     * @param string $status
     * @param bool $reconnect
     * @return bool
     */
    public function changeStatus($status, $reconnect = false)
    {
        if ($reconnect) $this->reconnect();
        $this->set('status', $status);
        if ($saved = $this->save()) {
            $this->sendNotice();
        }
        return $saved;
    }

    /**
     * @return bool
     */
    public function sendNotice()
    {
        if (!$preset = $this->getPreset()) return false;
        if (!$preset->getSetting('notice', 0)) return;
        if (!$noticeStatus = $preset->getSetting('notice_status', array())) return;
        if (!in_array($this->get('status'), $noticeStatus)) return;
        if (!$noticeMethod = $preset->getSetting('notice_method', '')) return false;
        $noticeMethod .= 'Notice';
        if (!method_exists($this, $noticeMethod)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, "Task ID: {$this->get('id')} notice method: {$noticeMethod} not found!");
            return false;
        }

        $data = $this->prepareNoticeData();
        return call_user_func(array($this, $noticeMethod), $data);
    }

    /**
     * @return array
     */
    public function prepareNoticeData()
    {
        $data = $this->toArray();
        $preset = $this->getPreset();
        $report = $this->getReport(true);

        $data['stats'] = array();
        $data['mode'] = $preset->get('mode');
        $data['preset_name'] = $preset->get('name');
        $data['preset_description'] = $preset->get('description');
        $data['status_text'] = $this->xpdo->lexicon('msimportexport_task_status_' . $this->get('status'));
        $data['start_time'] = $data['start_time'] * 10000;
        $data['finish_time'] = $data['finish_time'] * 10000;
        $data['total_time'] = 0;

        if ($data['start_time'] && $data['finish_time']) {
            $data['total_time'] = $data['finish_time'] - $data['start_time'];
            $data['total_time'] = $this->msie->getTools()->formatMilliseconds($data['total_time']);
        }

        if ($report) {
            $report['stats_total']['iteration'] = $report['iteration'];
            $report['stats_total']['restarted'] = $data['restarted'];
            foreach ($report['stats_total'] as $key => $val) {
                $k = $this->xpdo->lexicon('msimportexport_task_dashboard_' . $key);
                $k = $k ? $k : $key;
                $data['stats'][$k] = $val;
            }

        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function emailNotice(array $data)
    {
        if (!$preset = $this->getPreset()) return false;
        $emails = $preset->getSetting('notice_email', '');
        if (!$emails = $this->msie->getTools()->explodeAndClean($emails)) return false;
        if (!$pdoTools = $this->msie->getTools()->getPdoTools()) return false;

        $subject = $pdoTools->getChunk('@INLINE ' . $preset->getSetting('notice_template_subject', ''), $data);
        $message = $pdoTools->getChunk('@INLINE ' . $preset->getSetting('notice_template_message', ''), $data);

        foreach ($emails as $email) {
            $this->msie->getTools()->sendEmail($email, $subject, $message);
        }

        return true;
    }

    public function shutdown()
    {
        $err = error_get_last();
        $state = $this->worker->getState();
        $this->saveState($state);
        if (!empty($err)) {
            if (in_array($err['type'], array(E_ERROR))) {
                $this->putInStdErr($err);
                if ($this->get('restarted') >= $this->autoRestartLimit) {
                    $this->changeStatus(self::STATUS_FAILED, true);
                } else {
                    $this->set('restarted', ($this->get('restarted') + 1));
                    $this->changeStatus(self::STATUS_WAITING, true);
                }
            }
        }
    }

    /**
     * @param string $method
     * @return bool|int
     */
    public function call($method)
    {
        if (!method_exists($this, $method)) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR, "Method: {$method} not found!");
            return false;
        }
        $binary = $this->getBinary();
        $runner = $this->getRunner();
        $stdout = $this->getStdOut();
        $stderr = $this->getStdErr();
        $output = $this->exec("{$binary} {$runner} %s %s {$stdout} {$stderr} & echo $!", array($this->get('id'), $method));
        return isset($output[0]) ? $output[0] : 0;
    }

    /**
     * Executes a command after filtering all parameters.
     * @param string $format
     * @param array $parameters
     * @return string
     */
    public function exec($format, array $parameters = [])
    {
        $parameters = array_map("escapeshellarg", $parameters);
        array_unshift($parameters, $format);
        $command = call_user_func_array("sprintf", $parameters);
        exec($command, $output);
        return $output;
    }

    /**
     * @param string $runner
     *
     * @return $this
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * Gets the path of the runner script
     *
     * @return string
     */
    public function getRunner()
    {
        if (!$this->runner) {
            $this->runner = $this->config['taskRunner'];
        }
        return $this->runner;
    }

    /**
     * @param string $binary
     *
     * @return $this
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * Gets the path of the PHP runtime.
     *
     * @return string
     */
    // TODO get option
    public function getBinary()
    {
        if (!$this->binary) {
            $this->binary = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
        }

        return $this->binary;
    }

    /**
     * @return array|void
     */
    public function getPidInfo()
    {
        if (!$this->get('pid')) return;
        $output = $this->exec("ps -o pid,%%cpu,%%mem,state,start -p %s | sed 1d", array(
            $this->get('pid'),
        ));
        if (count($output) < 1) return;
        $last = $output[count($output) - 1];
        if (trim($last) === '') return;
        $parts = preg_split("/\s+/", trim($last));
        $pid = intval($parts[0]);
        if ("{$pid}" !== $parts[0]) return;
        return $parts;
    }

    public function getState()
    {
        $state = array();
        $file = $this->getStateFile();
        if (file_exists($file)) {
            if ($content = file_get_contents($file)) {
                $state = unserialize($content);
            }
        }
        return $state;
    }

    /**
     * @return array
     */
    public function getStateStorage()
    {
        $storage = [];
        $state = $this->getState();
        if ($state) {
            $storage = $state['storage'] ?? '';
            if ($storage) {
                $storage = unserialize($storage);
                if (!is_array($storage)) {
                    $storage = [];
                }
            }
        }
        return $storage;
    }

    /**
     * @param array $state
     * @return bool
     */
    public function saveState(array $state = array())
    {
        $file = $this->getStateFile();
        return file_put_contents($file, serialize($state)) ? true : false;
    }

    /**
     * @return string
     */
    public function getStateFile()
    {
        return $this->getPath() . 'state.var';
    }

    /**
     * @param bool $last
     * @return array
     */
    function getReport($last = false)
    {
        $report = '';
        $file = $this->getReportFile();
        if (file_exists($file)) {
            $report = file_get_contents($file);
        }
        $report = $report ? $this->xpdo->fromJSON('[' . $report . ']') : array();
        if ($report && $last) {
            $report = array_pop($report);
        }
        return $report;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function saveReport(array $data = array())
    {
        $ok = true;
        $file = $this->getReportFile();
        $splitter = file_exists($file) ? ',' : '';
        $content = $splitter . $this->xpdo->toJSON($data);
        if ($fh = fopen($file, 'a')) {
            if (!fwrite($fh, $content)) {
                $ok = false;
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Error write report data');
            }
            fclose($fh);
        } else {
            $ok = false;
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Error open report file: ' . $file);
        }
        return $ok;
    }

    /**
     * @return string
     */
    public function getReportFile()
    {
        return $this->getPath() . 'report.var';
    }


    /**
     * @return bool|false|string
     */
    public function getLogErrors()
    {
        $log = '';
        $file = $this->getLogPath() . 'stderr.log';
        if (file_exists($file)) {
            $log = file_get_contents($file);
        }
        return $log;
    }

    public function clearLogErrors()
    {
        $file = $this->getLogPath() . 'stderr.log';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @return null|string
     */
    public function getLogPath()
    {
        $path = $this->getPath() . 'log' . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            $this->xpdo->getCacheManager()->writeTree($path);
        }
        return $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $path = $this->config['dataPath'] . 'task' . DIRECTORY_SEPARATOR . $this->get('id') . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            $this->xpdo->getCacheManager()->writeTree($path);
        }
        return $path;
    }

    /**
     * Gets the path to write stdout to.
     *
     * @return string
     */
    public function getStdOut()
    {
        if ($this->getLogPath() !== null) {
            return '>> ' . $this->getLogPath() . 'stdout.log';
        }

        return '> /dev/null';
    }

    /**
     * Gets the path to write stderr to.
     *
     * @return string
     */
    public function getStdErr()
    {
        if ($this->getLogPath() !== null) {
            return '2>> ' . $this->getLogPath() . 'stderr.log';
        }

        return '2> /dev/null';
    }

    /**
     * @param string $error
     * @return bool
     */
    protected function putInStdErr($error)
    {
        if (is_array($error)) {
            $error = print_r($error, 1);
        }
        $error .= "\n";
        $file = $this->getLogPath() . 'stderr.log';
        return file_put_contents($file, $error, FILE_APPEND) ? true : false;
    }

    /**
     * @return bool
     */
    public function isCli()
    {
        return php_sapi_name() == 'cli' ? true : false;
    }

    public function reconnect()
    {
        MsIeTools::reconnect($this->xpdo, $this->sessionWaitTimeout);
    }

}