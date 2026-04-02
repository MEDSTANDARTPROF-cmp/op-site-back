<?php

class msImportExportTaskRemoveProcessor extends modObjectUpdateProcessor
{
    public $languageTopics = array('msimportexport:default');
    public $classKey = 'MsieTask';
    public $permission = '';

    public function initialize()
    {
        /* if (!$this->modx->hasPermission($this->permission)) {
             return $this->modx->lexicon('access_denied');
         }*/
        return parent::initialize();
    }

    public function beforeSet()
    {
        $status = $this->object->get('status');
        $statuses = array(MsieTask::STATUS_RUNNING, MsieTask::STATUS_WAITING_KILL);
        if (!in_array($status, $statuses)) {
            $this->setProperty('deleted', 1);
        }
        return true;
    }

}

return 'msImportExportTaskRemoveProcessor';