<?php

class msImportExportTaskClearLogProcessor extends modObjectGetProcessor
{
    public $classKey = 'MsieTask';
    public $languageTopics =array('msimportexport:default');

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        $this->object->clearLogErrors();
        return $this->success('', array('log' => ''));
    }

}

return 'msImportExportTaskClearLogProcessor';