<?php

class msImportExportCronMultipleProcessor extends modProcessor
{
    public $classKey = 'MsieCron';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function process()
    {

        if (!$method = $this->getProperty('method', false)) {
            return $this->failure();
        }
        $ids = json_decode($this->getProperty('ids'), true);
        if (empty($ids)) {
            return $this->success();
        }

        foreach ($ids as $id) {
            $this->modx->error->reset();
            /** @var modProcessorResponse $response */
            $response = $this->msie->runProcessor('mgr/cron/' . $method, array('id' => $id));
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }
}

return 'msImportExportCronMultipleProcessor';