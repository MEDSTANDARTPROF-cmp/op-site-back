<?php

class MsIeODSWriter extends MsIeXLSXWriter
{
    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_ODS;
    }

}
