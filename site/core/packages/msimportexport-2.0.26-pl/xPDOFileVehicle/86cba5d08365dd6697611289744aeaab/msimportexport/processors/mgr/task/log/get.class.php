<?php

class msImportExportTaskGetLogProcessor extends modObjectGetProcessor
{
    public $classKey = 'MsieTask';
    public $languageTopics = array('msimportexport:default');

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        $log = $this->object->getLogErrors();
        return $this->success('', array('log' => $log));
    }

}

return 'msImportExportTaskGetLogProcessor';