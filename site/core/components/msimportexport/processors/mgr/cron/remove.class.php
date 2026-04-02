<?php

class msImportExportCronRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'MsieCron';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        // $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }
}

return 'msImportExportCronRemoveProcessor';