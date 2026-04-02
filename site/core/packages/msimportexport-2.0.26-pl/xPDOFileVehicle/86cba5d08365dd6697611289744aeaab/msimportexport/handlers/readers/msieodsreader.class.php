<?php

use OpenSpout\Reader\ODS\Reader;

class MsIeODSReader extends MsIeXLSXReader
{
    /** @var Reader $reader */
    protected $reader;

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_ODS;
    }

}