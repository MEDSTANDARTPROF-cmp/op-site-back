<?php

class msImportExportTaskCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'MsieTask';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        // $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }
}

return 'msImportExportTaskCreateProcessor';