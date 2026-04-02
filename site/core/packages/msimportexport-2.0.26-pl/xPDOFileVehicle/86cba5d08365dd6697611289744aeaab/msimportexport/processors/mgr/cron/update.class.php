<?php
class msImportExportCronUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'MsieCron';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        // $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function beforeSet() {
        $this->setCheckbox('active');
        return parent::beforeSet();
    }

}
return 'msImportExportCronUpdateProcessor';