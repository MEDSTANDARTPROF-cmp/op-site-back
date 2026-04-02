<?php

use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Common\Creator\ReaderFactory;
use OpenSpout\Reader\XLSX\Reader;

class MsIeXLSXReader extends MsIeReader
{
    /** @var Reader $reader */
    protected $reader;
    protected $onlyFirstSheet = false;
    protected $skip_sheet = [];

    /**
     * @param array $config
     */
    public function initialize(array $config = array())
    {
        parent::initialize($config);
        $this->onlyFirstSheet = $this->config["xlsx_only_first_sheet"] ?? false;
        $skip_sheet = $this->config["xlsx_skip_sheet"] ?? '';
        $this->skip_sheet = explode(',', $skip_sheet);
        try {
            $this->reader = ReaderFactory::createFromType($this->getReaderType());
        } catch (UnsupportedTypeException $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
        }
    }

    /**
     * @param string $file
     * @return bool
     */
    public function open($file)
    {
        if (!parent::open($file)) return false;
        try {
            $this->reader->open($file);
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return false;
        }
        return true;
    }

    /**
     * @param callable|null $callback
     * @return bool|mixed
     */
    public function read(callable $callback = null)
    {
        $result = false;
        $this->proceed = true;
        try {
            $idx = 0;
            foreach ($this->reader->getSheetIterator() as $sheet) {
                if (!empty($sheet->getName()) && !empty($this->skip_sheet) && in_array($sheet->getName(), $this->skip_sheet)) {
                    continue;
                }
                foreach ($sheet->getRowIterator() as $row) {
                    $idx++;
                    if ($idx < $this->offset) continue;
                    $this->offset++;
                    if (!$this->proceed) break 2;
                    $this->record = $row->toArray();
                    if (is_callable($callback)) {
                        if ($callback($this->record, $this) !== true) {
                            $this->close();
                            return true;
                        }
                    } else {
                        $this->fireEvent('read', array('data' => $this->record));
                    }

                }
                if ($this->onlyFirstSheet) {
                    break;
                }
            }
            $result = true;
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
        }
        return $result;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset = 0)
    {
        if (empty($offset)) $offset = 1;
        $this->offset = $offset;

    }

    public function close()
    {
        if ($this->reader) {
            $this->reader->close();
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
    protected function getReaderType()
    {
        return $this->getType();
    }
}