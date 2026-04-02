<?php

class msImportExportTaskRunProcessor extends modObjectGetProcessor
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

    public function cleanup()
    {
        if ($this->object->changeStatus(MsieTask::STATUS_WAITING)) {
            return $this->success('', $this->object->toArray());
        }
        return $this->failure('Error change status task!');
    }

}

return 'msImportExportTaskRunProcessor';