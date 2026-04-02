<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class  msImportExportResourceDeleteProcessor extends modResourceDeleteProcessor
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

}

return 'msImportExportResourceDeleteProcessor';
