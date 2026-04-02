<?php

class msImportExportWatcherMultipleProcessor extends modProcessor
{
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
        $pids = json_decode($this->getProperty('pids'), true);
        if (empty($pids)) {
            return $this->success();
        }

        foreach ($pids as $pid) {
            $this->modx->error->reset();
            /** @var modProcessorResponse $response */
            $response = $this->msie->runProcessor('mgr/watcher/' . $method, array('pid' => $pid));
            if ($response->isError()) {
                return $response->getResponse();
            }
        }

        return $this->success();
    }
}

return 'msImportExportWatcherMultipleProcessor';