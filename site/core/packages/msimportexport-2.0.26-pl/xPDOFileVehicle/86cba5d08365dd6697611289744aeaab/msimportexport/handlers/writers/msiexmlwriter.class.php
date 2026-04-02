<?php

class MsIeXMLWriter extends MsIeWriter
{


    /**
     * @param array $data
     * @param array $options
     */
    public function write(array $data, array $options = array())
    {

    }

    /**
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function save($path, array $options = array())
    {
        return true;
    }


    public function close()
    {

    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_XML;
    }
}
