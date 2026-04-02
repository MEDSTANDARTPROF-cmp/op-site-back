<?php

class msImportExportFileExistsProcessor extends modProcessor
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

        $file = $this->getProperty('file');
        $ok = $this->msie->getTools()->fileExists($file);
        return $ok ? $this->success('') : $this->failure($this->modx->lexicon('msimportexport_system_err_nf_file', array('file' => $file)));
    }
}

return 'msImportExportFileExistsProcessor';
