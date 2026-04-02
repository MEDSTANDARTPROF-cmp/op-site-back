<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/emptyrecyclebin.class.php';


/**
 * Empties the recycle bin.
 *
 * @return boolean
 *
 * @package modx
 * @subpackage processors.resource
 */
class msImportExportResourceEmptyRecycleBinProcessor extends modResourceEmptyRecycleBinProcessor
{
    public function checkPermissions()
    {
        return true;
    }
}

return 'msImportExportResourceEmptyRecycleBinProcessor';
