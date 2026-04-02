<?php

class msImportExportTaskMultipleProcessor extends modProcessor
{
    public $classKey = 'MsieTask';
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
        $params = $this->getProperties();
        foreach ($ids as $id) {
            $this->modx->error->reset();
            $params['id'] = $id;
            /** @var modProcessorResponse $response */
            $response = $this->msie->runProcessor('mgr/task/' . $method, $params);
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }
}

return 'msImportExportTaskMultipleProcessor';