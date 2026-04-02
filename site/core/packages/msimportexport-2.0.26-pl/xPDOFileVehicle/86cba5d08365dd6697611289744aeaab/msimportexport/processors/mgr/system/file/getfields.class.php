<?php

class msImportExportFileGetFieldsProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;
    /** @var MsiePreset $preset */
    public $preset;
    /** @var MsIeService $service */
    public $service;

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

        $preset = $this->getProperty('preset', 0);
        $file = $this->getProperty('file');
        if (!$this->preset = $this->modx->getObject('MsiePreset', $preset)) {
            return $this->failure('Error not find preset ID ' . $preset);
        }

        if (!$this->service = $this->msie->getService($this->preset->get('mode'), $this->preset->get('service'))) {
            $err = $this->modx->lexicon('msimportexport_service_err_nf', array('service' => $this->preset->get('service')));
            return $this->failure($err);
        }

        $data = array(
            'fields' => $this->parseFileFields($file),
            'values' => $this->preset->get('fields'),
        );

        return $this->success('', $data);
    }

    /**
     * @param $file
     * @return array
     * @throws Exception
     */
    public function parseFileFields($file)
    {
        $fields = array();
        if (!$worker = $this->service->getWorker()) {
            $err = $this->modx->lexicon('msimportexport_service_err_worker', array('service' => $this->preset->get('service')));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
        } else {
            $fields = $worker->getFileFields($file, $this->preset->getSettings());
        }
        return $fields;
    }

}

return 'msImportExportFileGetFieldsProcessor';
