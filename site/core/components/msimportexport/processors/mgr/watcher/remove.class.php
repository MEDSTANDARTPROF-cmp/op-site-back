<?php

class msImportExportWatcherRemoveProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;
    /** @var  MsIeWatcher $watcher */
    protected $watcher;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $this->watcher = $this->msie->getWatcher();
        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        if ($this->watcher) {
            $pid = $this->getProperty('pid', 0);
            if (!$this->watcher->remove($pid)) {
                $err = $this->modx->lexicon('msimportexport_watcher_err_remove', array('pid' => $pid));
                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                return $this->failure($err);
            }
        }
        return $this->success('', array());
    }
}

return 'msImportExportWatcherRemoveProcessor';