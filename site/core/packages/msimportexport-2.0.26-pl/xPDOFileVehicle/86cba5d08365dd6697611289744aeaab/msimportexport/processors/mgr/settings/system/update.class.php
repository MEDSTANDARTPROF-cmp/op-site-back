<?php

class msImportExportSettingsSystemUpdateProcessor extends modProcessor
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

    public function process()
    {
        $settings = $this->getProperty('settings', '[]');
        $this->msie->getTools()->setOption('system_settings', $settings, '', true);
        return $this->success($this->modx->lexicon('msimportexport_settings_success_save'));
    }
}

return 'msImportExportSettingsSystemUpdateProcessor';
