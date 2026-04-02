<?php

abstract class MsIeReader
{
    const TYPE_CSV = 'csv';
    const TYPE_XLS = 'xls';
    const TYPE_XLSX = 'xlsx';
    const TYPE_ODS = 'ods';

    /** @var modX $modx */
    public $modx;
    /** @var string $file */
    protected $file;
    /** @var int $offset */
    protected $offset = 0;
    /** @var int $totalRows */
    protected $totalRows = null;
    /** @var array $eventStack */
    protected $eventStack = array();
    /** @var bool $proceed */
    protected $proceed = true;
    /** @var array $config */
    protected $config = array();
    /**@var array $record */
    protected $record = array();

    /**
     * MsieReader constructor.
     * @param $modx
     * @param array $config
     */
    public function __construct(& $modx, $config = array())
    {
        $this->modx = &$modx;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param array $config
     */
    public function initialize(array $config = array())
    {
        $this->config = array_merge($this->config, $config);
        $this->clearEvents();
    }

    /**
     * @param string $file
     * @return bool
     */
    public function open($file)
    {
        if (!file_exists($file)) {
            $err = $this->modx->lexicon('msimportexport_system_err_nf_file', array('file' => $this->file));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            return false;
        }
        $this->file = $file;
        return true;
    }

    /**
     * @param callable|null $callback
     * @return bool|mixed
     */
    public function read(callable $callback = null)
    {
        $this->proceed = true;
        return true;
    }

    /**
     * @return array
     */
    public function getReadRecord()
    {
        return $this->record;
    }

    public function stop()
    {
        $this->proceed = false;
    }

    /**
     * @return bool
     */
    public function stopped()
    {
        return !$this->proceed;
    }

    public function close()
    {
    }

    /**
     * @param string $event
     * @param callable $callback
     * @return $this
     */
    public function onEvent($event, callable $callback)
    {
        if (!isset($this->eventStack[$event]))
            $this->eventStack[$event] = array();
        $this->eventStack[$event][] = $callback;
        return $this;
    }

    /**
     * @param string $event
     * @param array|null $params
     * @param bool $once
     * @return bool
     */
    public function fireEvent($event, $params = null, $once = false)
    {
        if ($params == null)
            $params = array();
        $params['reader'] = $this;
        if (!isset($this->eventStack[$event]))
            return false;
        $count = count($this->eventStack[$event]);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                call_user_func_array($this->eventStack[$event][$i], array_values($params));
                if ($once == true) {
                    array_splice($this->eventStack[$event], $i, 1);
                }
            }
        }
    }

    /**
     * @param string $event
     * @return $this
     */
    public function unbindEvent($event)
    {
        unset($this->eventStack[$event]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clearEvents()
    {
        $this->eventStack = array();
        return $this;
    }

    /**
     * @return array
     */
    static public function getExtensions()
    {
        return array(self::TYPE_CSV, self::TYPE_XLS, self::TYPE_XLSX, self::TYPE_ODS);
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }
}

