<?php
class msImportExportTaskUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'MsieTask';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        // $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function beforeSet() {
        //$this->setCheckbox('enable');
        return parent::beforeSet();
    }

}
return 'msImportExportTaskUpdateProcessor';