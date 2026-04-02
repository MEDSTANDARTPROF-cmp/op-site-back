<?php

use OpenSpout\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Creator\WriterFactory;
use OpenSpout\Writer\Common\Creator\WriterEntityFactory;

class MsIeXLSXWriter extends MsIeWriter
{

    /* @var Writer\XLSX\Writer|Writer\CSV\Writer|Writer\ODS\Writer $writer */
    public $writer;
    /** @var string $tmpfile */
    protected $tmpfile;

    /**
     * @param array $config
     * @return bool
     */
    public function initialize(array $config = array())
    {
        if ($ok = parent::initialize($config)) {
            try {
                $tmpPath = $this->modx->getOption('tmp_path', $this->config, $this->getSysTmpPath(), true);
                $this->tmpfile = tempnam($tmpPath, 'msie_') . '.' . $this->getTmpFileType();
                $writerType = $this->getWriterType();
                if ($this->writer = WriterFactory::createFromType($writerType)) {
                    if ($writerType == MsIeTools::FILE_TYPE_XLSX || $writerType == MsIeTools::FILE_TYPE_ODS) {
                        $this->writer->setShouldUseInlineStrings(true);
                        if ($tmpPath) {
                            $this->writer->setTempFolder($tmpPath);
                        }
                    }
                    $this->writer->openToFile($this->tmpfile);
                    $this->setSheetName('export');
                } else {
                    return false;
                }
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
                return false;
            }
        }
        return $ok;
    }

    /**
     * @param array $data
     * @param array $options
     * @return Row|null
     */
    public function write(array $data, array $options = array())
    {
        if (!$this->writer) return null;
        try {
            $this->offset++;
            $row = WriterEntityFactory::createRowFromArray($data);
            $this->writer->addRow($row);
            return $row;
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return null;
        }
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        while ($this->offset < $offset) {
            try {
                $row = WriterEntityFactory::createRowFromArray(array());
                $this->writer->addRow($row);
                $this->offset++;
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            }
        }
    }

    /**
     * @return Writer\Common\Entity\Sheet|null
     */
    public function getSheet()
    {
        try {
            return $this->writer->getCurrentSheet();
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return null;
        }
    }

    /**
     * @param string $name
     * @throws Writer\Exception\InvalidSheetNameException
     */
    public function setSheetName($name)
    {
        if ($sheet = $this->getSheet()) {
            $sheet->setName($name);
        }
    }

    /**
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function save($path, array $options = array())
    {
        try {
            $this->close();
            $path = $this->preparePath($path);
            $this->modx->cacheManager->copyFile($this->tmpfile, $path);
            if (file_exists($this->tmpfile)) {
                unlink($this->tmpfile);
            }
            return true;
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return false;
        }
    }


    public function close()
    {
        if ($this->writer) {
            $this->writer->close();
            $this->writer = null;
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_XLSX;
    }

    /**
     * @return string
     */
    protected function getTmpFileType()
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    protected function getWriterType()
    {
        return $this->getType();
    }
}
