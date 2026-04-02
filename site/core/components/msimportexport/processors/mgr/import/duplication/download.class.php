<?php

class msImportExportDuplicationDownload extends modProcessor
{

    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    /**
     * @return array|string
     */
    public function process()
    {

        $taskId = $this->getProperty('task', 0);
        /** @var MsieTask $task */
        $task = $this->modx->getObject('MsieTask', $taskId);

        if (!$task) {
            return $this->failure($this->modx->lexicon('msimportexport_task_err_nf', array('id' => $taskId)));
        }


        if (!$taskManager = $this->msie->getTaskManager()) {
            return $this->failure();
        }

        $options = array(
            'label' => 'duplication',
            'settings' => array(
                'service' => $task->Preset->getSetting('service', 'MsIeResourceDuplicationService'),
                'source_task' => $taskId,
                'iteration_report' => 2,
            ),
        );

        if (!$task = $taskManager->add($task->get('preset_id'), $options)) {
            return $this->failure($this->modx->lexicon(('msimportexport_task_err_create')));
        }

        return $this->success('', $task);

    }

}

return 'msImportExportDuplicationDownload';