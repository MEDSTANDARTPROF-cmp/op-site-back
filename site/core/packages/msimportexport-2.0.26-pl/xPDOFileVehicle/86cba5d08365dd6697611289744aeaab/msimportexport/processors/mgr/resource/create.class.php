<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
include_once MODX_CORE_PATH . 'model/modx/processors/resource/create.class.php';

class msImportExportResourceCreateProcessor extends modResourceCreateProcessor
{
    public $permission = '';

    public static function getInstance(modX &$modx, $className, $properties = array())
    {
        $className = __CLASS__;
        $processor = new $className($modx, $properties);
        return $processor;
    }

    public function checkPermissions()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function cleanup() {
        $this->object->removeLock();
       // $this->clearCache();
        return $this->success('', array('id' => $this->object->get('id')));
    }

}

return 'msImportExportResourceCreateProcessor';
