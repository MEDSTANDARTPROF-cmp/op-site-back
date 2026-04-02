<?php

class MsIeCSVWriter extends MsIeXLSXWriter
{

    /**
     * @param array $config
     * @return bool
     */
    public function initialize(array $config = array())
    {
        if ($ok = parent::initialize($config)) {
            $delimiter = $this->modx->getOption('csv_delimiter', $this->config, ';', true);
            $enclosure = $this->modx->getOption('csv_enclosure', $this->config, '"', true);
            $this->writer->setFieldDelimiter($delimiter);
            $this->writer->setFieldEnclosure($enclosure);
        }
        return $ok;
    }

    public function getSheet()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_CSV;
    }

}
