<?php

class msImportExportRunExport extends modProcessor
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

        $options = array(
            'settings' => array(),
        );

        $format = $this->getProperty('format', '');
        $presetId = $this->getProperty('preset', 0);

        if (!$presetId) {
            return $this->failure($this->modx->lexicon('msimportexport_task_err_preset_ns'));
        }

        /** @var MsiePreset $preset */
        if (!$preset = $this->modx->getObject('MsiePreset', $presetId)) {
            return $this->failure($this->modx->lexicon('msimportexport_task_err_preset_nf'));
        }

        if (!$taskManager = $this->msie->getTaskManager()) {
            return $this->failure();
        }

        if ($format) {
            $options['settings']['export_format'] = $format;
        } else if (empty($preset->getSetting('export_format'))) {
            return $this->failure($this->modx->lexicon('msimportexport_export_err_file_format_ns'));
        }

        if (!$task = $taskManager->add($presetId, $options)) {
            return $this->failure($this->modx->lexicon(('msimportexport_task_err_create')));
        }

        return $this->success('', $task);
    }

}

return 'msImportExportRunExport';