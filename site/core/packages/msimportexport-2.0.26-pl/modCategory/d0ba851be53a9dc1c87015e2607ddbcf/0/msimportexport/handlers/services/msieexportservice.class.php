<?php

abstract class MsIeExportService extends MsIeService
{

    /**
     * @return array
     */
    public function getExcludeFields()
    {
        $fields = $this->tools->getOption('export_exclude_fields');
        $fields = $this->tools->explodeAndClean($fields);
        return $fields ? $fields : array();
    }

}