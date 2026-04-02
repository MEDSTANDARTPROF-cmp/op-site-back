<?php

class  msImportExportPresetCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'MsiePreset';
    public $languageTopics = array('msimportexport:preset');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function beforeSet()
    {
        $mode = $this->getProperty('mode');
        $settings = $this->modx->fromJSON($this->msie->getTools()->getOption($mode . '_default_settings', null, '{}', true));
        $this->setProperty('fields', array());
        $this->setProperty('settings', $settings);
        return parent::beforeSet();
    }
}

return 'msImportExportPresetCreateProcessor';