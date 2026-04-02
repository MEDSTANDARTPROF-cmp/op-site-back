<?php

class MsIeTSVReader extends MsIeXLSXReader
{

    /** @var Box\Spout\Reader\CSV\Reader $reader */
    protected $reader;

    public function initialize(array $config = array())
    {
        parent::initialize($config);
        $delimiter = "\t";
        $enclosure = $this->modx->getOption('csv_enclosure', $this->config, '"', true);
        $this->reader->setFieldDelimiter($delimiter);
        $this->reader->setFieldEnclosure($enclosure);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_TSV;
    }

    protected function getReaderType()
    {
        return MsIeTools::FILE_TYPE_CSV;
    }


}
