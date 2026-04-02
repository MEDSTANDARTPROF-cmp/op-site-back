<?php

class msImportExportRunImport extends modProcessor
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
     * @return array|mixed|string
     * @throws Exception
     */
    public function process()
    {

        $preset = $this->getProperty('preset', 0);
        $file = $this->getProperty('file', '');

        if (!$preset) {
            return $this->failure($this->modx->lexicon('msimportexport_task_err_preset_ns'));
        }

        if (!$file) {
            return $this->failure($this->modx->lexicon(('msimportexport_task_err_working_directory_ns')));
        }

        if (!$taskManager = $this->msie->getTaskManager()) {
            return $this->failure();
        }

        $options = array(
            'settings' => array(
                'working_directory' => $this->msie->getTools()->getWorkingDirectoryByFile($file)
            ),
        );

        if (!$task = $taskManager->add($preset, $options)) {
            return $this->failure($this->modx->lexicon(('msimportexport_task_err_create')));
        }

        return $this->success('', $task);
    }

}

return 'msImportExportRunImport';