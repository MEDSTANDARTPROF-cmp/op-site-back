<?php

class MsIeTaskManager
{
    /** @var modX $modx */
    public $modx;
    /** @var Msie $msie */
    public $msie;

    /**
     * MsIeTaskManager constructor.
     * @param Msie $msie
     */
    public function __construct(Msie &$msie)
    {
        $this->msie = &$msie;
        $this->modx = &$msie->modx;
        $this->modx->lexicon->load('msimportexport:preset');
    }

    /**
     * @param int $taskId
     * @return MsieTask|null
     */
    public function getTask($taskId)
    {
        return $this->modx->getObject('MsieTask', $taskId);
    }

    /**
     * @param int $presetId
     * @param array $options
     * @return MsieTask|bool
     */
    public function add($presetId, array $options = array())
    {
        $label = $this->modx->getOption('label', $options, '', true);
        $cronId = $this->modx->getOption('cron_id', $options, 0, true);
        $settings = $this->modx->getOption('settings', $options, array(), true);
        $properties = $this->modx->getOption('properties', $options, array(), true);


        /** @var MsiePreset $preset */
        if (!$preset = $this->modx->getObject('MsiePreset', $presetId)) {
            $err = $this->modx->lexicon('msimportexport_preset_err_nf', array('id' => $presetId));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            return false;
        }

        $settings = array_merge($preset->getSettings(), $settings);

        if ($preset->get('mode') == Msie::MODE_IMPORT && !empty($settings['file'])) {
            $data = array(
                'preset' => $presetId,
                'file' => $settings['file'],
            );

            /** @var modProcessorResponse $response */
            $response = $this->msie->runProcessor('mgr/system/file/upload', $data);
            if ($response->isError()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
                return false;
            }

            $result = $response->getObject();
            $settings['working_directory'] = $result['path'];
        }


        $task = $this->modx->newObject('MsieTask');
        $task->set('preset_id', $presetId);
        $task->set('cron_id', $cronId);
        $task->set('label', $label);
        $task->set('settings', $settings);
        $task->set('properties', $properties);

        if ($task->save()) {
            return $task;
        }

        return false;
    }

    /**
     * @param $taskId
     * @return bool|int
     * @throws Exception
     */
    public function run($taskId)
    {
        if ($task = $this->getTask($taskId)) {
            $daemonMode = $this->msie->getTools()->checkDaemonMode();
            if ($daemonMode) {
                return $task->call('run');
            } else {
                return $task->run();
            }
        }
        return false;
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function stop($taskId)
    {
        if ($task = $this->getTask($taskId)) {
            return $task->stop();
        }
        return false;
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function isRunning($taskId)
    {
        if ($task = $this->getTask($taskId)) {
            return $task->isRunning();
        }
    }

    /**
     * @param int $taskId
     * @return bool
     */
    public function kill($taskId)
    {
        if ($task = $this->getTask($taskId)) {
            return $task->kill();
        }
        return false;
    }

    /**
     * @param array $options
     */
    public function gc(array $options = array())
    {

        $classKey = 'MsieTask';
        $maxLifeTime = $this->modx->getOption('maxLifeTime', $options, $this->msie->getTools()->getSysSetting('gc_task_maxlifetime'));
        $time = str_replace(',', '.', microtime(true));
        if ($maxLifeTime) {
            $q = $this->modx->newQuery($classKey);
            $q->where(array(
                'status:IN' => array(MsieTask::STATUS_COMPLETED, MsieTask::STATUS_FAILED, MsieTask::STATUS_KILLED),
                "{$time} - start_time > {$maxLifeTime}"
            ));

            if ($tasks = $this->modx->getCollection($classKey, $q)) {
                foreach ($tasks as $task) {
                    $task->remove();
                }
            }
        }
    }


    /**
     * @param int $taskId
     * @return array|bool
     */
    //TODO http://blog.pridybailo.com/%D1%83%D1%87%D0%B8%D0%BC%D1%81%D1%8F-%D0%BF%D0%BE%D0%BD%D0%B8%D0%BC%D0%B0%D1%82%D1%8C-%D0%B2%D1%8B%D0%B2%D0%BE%D0%B4-ps/
    public function getStats($taskId)
    {
        if ($task = $this->getTask($taskId)) {
            return $task->getStats();
        }
        return false;
    }

    /**
     * @param int $presetId
     *
     * @return MsieTask|null
     */
    public function getLastCompletedTaskByPreset(int $presetId)
    {
        $classKey = 'MsieTask';
        $q = $this->modx->newQuery($classKey);
        $q->where(array(
            "`{$classKey}`.`preset_id`" => $presetId,
            "`{$classKey}`.`status`" => MsieTask::STATUS_COMPLETED,
        ));
        $q->sortby("`{$classKey}`.`finish_time`", 'DESC');
        return $this->modx->getObject($classKey, $q);
    }

}