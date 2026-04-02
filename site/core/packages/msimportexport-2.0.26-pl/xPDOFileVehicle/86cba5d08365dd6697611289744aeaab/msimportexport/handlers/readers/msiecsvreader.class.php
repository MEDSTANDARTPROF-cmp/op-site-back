<?php

class MsIeCSVReader extends MsIeXLSXReader
{

    /** @var OpenSpout\Reader\CSV\Reader $reader */
    protected $reader;

    public function initialize(array $config = array())
    {
        parent::initialize($config);
        $delimiter = $this->modx->getOption('csv_delimiter', $this->config, ';');
        $enclosure = $this->modx->getOption('csv_enclosure', $this->config, '"');
        $this->reader->setFieldDelimiter($delimiter);
        $this->reader->setFieldEnclosure($enclosure);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_CSV;
    }

}
