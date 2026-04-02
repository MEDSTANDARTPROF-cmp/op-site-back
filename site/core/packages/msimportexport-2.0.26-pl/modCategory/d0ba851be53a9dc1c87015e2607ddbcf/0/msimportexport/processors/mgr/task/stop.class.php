<?php

class msImportExportTaskStopProcessor extends modObjectGetProcessor
{
    public $classKey = 'MsieTask';
    /** @var MsieTask $object */
    public $object = null;
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    /**
     * Used for adding custom data in derivative types
     * @return void
     */
    public function beforeOutput()
    {
        $hard = $this->getProperty('hard', false);
        if ($hard) {
            /** @var  MsIeWatcher $watcher */
            if ($watcher = $this->msie->getWatcher()) {
                $watcher->addKillTask($this->object);
            }
        } else {
            $this->object->stop();
        }

    }
}

return 'msImportExportTaskStopProcessor';