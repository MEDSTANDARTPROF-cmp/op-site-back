<?php

abstract class MsIeImportService extends MsIeService
{

    /**
     * @return array
     */
    public function getExcludeFields()
    {
        $fields = $this->tools->getOption('import_exclude_fields');
        $fields = $this->tools->explodeAndClean($fields);
        return $fields ? $fields : array();
    }

}