<?php

abstract class MsIeWorker
{
    /** @var modX $modx */
    public $modx = null;
    /** @var Msie $msie */
    public $msie;
    /** @var MsIeTools $tools */
    public $tools;
    /** @var MsIeService $service */
    public $service;
    /** @var  MsieTask $task */
    public $task;
    /** @var  MsIeReader $reader */
    public $reader;
    /** @var  MsIeWriter $writer */
    public $writer;
    /** @var MsIeStorage $storage */
    public $storage;
    /** @var int $iteration */
    public $iteration = 0;
    /** @var array $fields */
    protected $fields = array();
    /** @var array $settings */
    protected $settings = array();
    /** @var array $properties */
    public $properties = array();
    /** @var string $file */
    protected $file;
    /** @var array $stats */
    protected $stats = array();
    /** @var array $lastStats */
    protected $lastStats = array();
    /** @var array $prepareFieldMethodList */
    protected $prepareFieldMethodList = array();
    /** @var  MsIeWorker[] */
    protected $subWorkers = array();
    /** @var int $debug */
    protected $debug;
    /** @var int $offset */
    protected $offset = 0;
    /** @var bool $stopped */
    protected $stopped = false;
    /** @var bool $completed */
    protected $completed = false;
    /** @var int $iterationReport */
    protected $iterationReport;
    /** @var string $eventError */
    protected $eventError;
    /** @var float $time */
    protected $time = 0;
    /** @var float $memory */
    protected $memory = 0;
    /** @var float $memoryPeak */
    protected $memoryPeak = 0;
    /** @var string $processorsPath */
    protected $processorsPath;

    /**
     * MsIeWorker constructor.
     * @param MsIeService $service
     * @param array $properties
     */
    public function __construct(MsIeService &$service, $properties = array())
    {

        $this->service = &$service;
        $this->modx = &$service->modx;
        $this->tools = &$service->tools;
        $this->msie = &$service->tools->msie;
        $this->storage = new MsIeArrStorage();
        $this->setProperties($properties);
        $this->processorsPath = $this->modx->getOption('processorsPath', $this->msie->config);

        if (function_exists("pcntl_signal")) {
            declare(ticks=1);
            pcntl_signal(SIGTERM, array($this, 'signalHandler'));
        }
    }

    /**
     * @return string
     */
    final public function getName()
    {
        return static::class;
    }

    abstract public function process();

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $this->time = microtime(true);
        $this->memory = memory_get_usage();
        $this->memoryPeak = memory_get_peak_usage();
        $this->debug = $this->getSetting('debug', 0);
        $this->iterationReport = $this->getSetting('iteration_report', 100);
        return true;
    }

    /**
     * @return bool
     */
    public function run()
    {
        $topics = $this->getLanguageTopics();
        foreach ($topics as $topic) {
            $this->modx->lexicon->load($topic);
        }
        $initialized = $this->initialize();
        if ($initialized !== true) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $initialized);
            return false;
        } else {
            return $this->process();
        }
    }

    /**
     * @param string $action Path to processor
     * @param array $data Data to be transmitted to the processor
     * @return mixed The result of the processor
     */
    public function runProcessor($action = '', $data = array())
    {
        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $this->processorsPath,
        ));
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    public function beforeStart()
    {
        $this->saveReport();
    }

    /**
     * @return bool
     */
    public function afterFinish()
    {
        return $this->saveReport();
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(), 1));
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function beforePrepareData(array $data)
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function afterPrepareData(array $data)
    {
        return $data;
    }

    /**
     * @param $file
     * @param array $options
     * @return MsIeReader|null
     */
    public function getReader($file, array $options = array())
    {
        return $this->tools->getReader($file, $options);
    }

    /**
     * @param string $file
     * @param array $settings
     * @return array
     */
    public function getFileFields($file, array $settings = array())
    {
        $fields = array();
        if (!$reader = $this->getReader($file)) return $fields;
        $reader->initialize($settings);
        $reader->open($file);
        $reader->onEvent('read', function ($data, $reader) use (&$fields) {
            $reader->stop();
            $fields = $data;
        });
        $reader->read();
        return $fields;
    }

    /**
     * @return array
     */
    public function getReadRecord()
    {
        $record = array();
        if ($this->reader) {
            $record = $this->reader->getReadRecord();
        }
        return $record;
    }

    /**
     * @param string $type
     * @param array $options
     * @return MsIeWriter|null
     */
    public function getWriter($type, array $options = array())
    {
        return $this->tools->getWriter($type, $options);
    }

    /**
     * @param MsieTask $task
     */
    public function setTask(MsieTask &$task)
    {
        $this->task = &$task;
    }


    public function fireEvent(string $event, array $params = array())
    {
        $this->eventError = '';

        $params = array_merge(array(
            'skip' => false,
            'worker' => $this,
            'presetId' => $this->task->get('preset_id'),
        ), $params);

        $response = $this->tools->invokeEvent($event, $params);

        if (empty($response['success'])) {
            $data = false;
            if (!empty($response['message'])) {
                $this->eventError = $response['message'];
                $this->modx->log(modX::LOG_LEVEL_ERROR, $this->eventError);
            }
        } else if (!empty($response['data']['skip'])) {
            $data = null;
        } else {
            $data = $response['data'];
        }

        unset($response, $params);

        return $data;
    }

    /**
     * @param bool $total
     * @return array
     */
    public function getStats($total = true)
    {
        if ($total) {
            return $this->stats;

        } else {
            $diff = array();
            if ($stats = $this->getStats()) {
                foreach ($stats as $key => $val) {
                    if (isset($this->lastStats[$key]) && is_numeric($val)) {
                        if (is_numeric($val)) {
                            $diff[$key] = $val - $this->lastStats[$key];
                        }
                    } else {
                        $diff[$key] = $val;
                    }
                }
            }
            $this->lastStats = $stats;
            return $diff;
        }
    }

    /**
     * @param array $stats
     */
    public function setStats(array $stats = array())
    {
        $this->stats = $stats;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param bool $skipEmpty
     * @return mixed|null
     */
    public function getStatsRecord($key, $default = null, $skipEmpty = true)
    {
        $val = array_key_exists($key, $this->stats) ? $this->stats[$key] : $default;
        if ($val === '' && $skipEmpty) {
            $val = $default;
        }
        return $val;
    }

    /**
     * @param string $key
     * @param mixed $val
     */
    public function setStatsRecord($key, $val)
    {
        $this->stats[$key] = $val;
    }

    /**
     * @param string $key
     * @param int $val
     * @return int
     */
    public function incrStatsRecord($key, $val = 1)
    {
        return $this->stats[$key] = $this->getStatsRecord($key, 0) + $val;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        if (is_array($fields)) {
            $this->fields = $fields;
        }
    }

    /**
     * @param mixed $field
     * @return int
     */
    public function addField($field)
    {
        if (empty($field)) return;
        return array_push($this->fields, $field);
    }


    /**
     * @param string|array $field
     * @param bool|int $index
     */
    public function insertField($field, $index = false)
    {
        if (empty($field)) return;
        if ($index === false) {
            array_splice($this->fields, count($this->fields), 0, $field);
        } else {
            array_splice($this->fields, $index, 0, $field);
        }
    }


    /**
     * @param $field
     * @return false|int
     */
    public function unsetField($field)
    {
        $index = array_search($field, $this->fields);
        if ($index !== false) {
            unset($this->fields[$index]);
        }
        return $index;
    }

    /**
     * @param string $field
     * @return false
     */
    public function hasField($field)
    {
        return array_search($field, $this->fields) !== false;
    }

    /**
     * @param $field
     *
     * @return false|int
     */
    public function getFieldIndex($field)
    {
        return array_search($field, $this->fields);

    }

    /**
     * @param string $field
     * @param object $context
     * @param string $method
     */
    public function addPrepareFieldMethod($field, $context, $method)
    {
        if (!isset($this->prepareFieldMethodList[$field])) {
            $this->prepareFieldMethodList[$field] = array();
        }
        $this->prepareFieldMethodList[$field][$method] = $context;
    }

    /**
     * @param string $field
     * @return array
     */
    public function getPrepareFieldMethods($field)
    {
        $methods = array();
        if ($this->hasPrepareFieldMethod($field)) {
            $methods = $this->prepareFieldMethodList[$field];
        }
        return $methods;
    }

    /**
     * @param string $field
     * @param string $method
     * @return bool
     */
    public function hasPrepareFieldMethod($field, $method = '')
    {
        $has = false;
        if ($this->prepareFieldMethodList) {
            if (isset($this->prepareFieldMethodList[$field])) {
                if ($method) {
                    if (isset($this->prepareFieldMethodList[$field][$method])) {
                        $has = true;
                    }
                } else {
                    $has = true;
                }
            }
        }
        return $has;
    }


    /**
     * @param string $field
     * @param string $method
     */
    public function removePrepareFieldMethod($field, $method)
    {
        if ($this->hasPrepareFieldMethod($field, $method)) {
            unset($this->prepareFieldMethodList[$field][$method]);
        }
    }

    /**
     * @param MsIeWorker $worker
     */
    public function copyWorkerScope(MsIeWorker $worker)
    {
        $this->task = $worker->task;
        $this->setSettings($worker->getSettings());
    }

    /**
     * @param MsIeWorker $worker
     * @param string $classKey
     * @param bool $copyScope
     */
    public function addSubWorker(MsIeWorker $worker, $classKey = '', $copyScope = true)
    {
        $classKey = $classKey ? $classKey : $worker->getName();
        if ($this->hasSubWorker($classKey)) return;
        if ($copyScope) {
            $worker->copyWorkerScope($this);
        }
        $this->subWorkers[$classKey] = $worker;
    }

    /**
     * @param string $classKey
     * @return MsIeWorker|null
     */
    public function getSubWorker($classKey)
    {
        if ($this->hasSubWorker($classKey)) {
            return $this->subWorkers[$classKey];
        }
        return null;
    }

    /**
     * @param string $classKey
     * @return bool
     */
    public function hasSubWorker($classKey)
    {
        return isset($this->subWorkers[$classKey]);
    }

    /**
     * @param string $classKey
     */
    public function removeSubWorker($classKey)
    {
        if ($this->hasSubWorker($classKey)) {
            unset($this->subWorkers[$classKey]);
        }
    }


    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * @param int $iteration
     */
    public function setIteration(int $iteration)
    {
        $this->iteration = $iteration;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile(string $file)
    {
        $this->file = $file;
    }

    /**
     * @param string $data
     */
    public function setStorage($data)
    {
        $storage = $this->storage->unserialize($data);
        $this->storage->setStorage($storage);
    }

    /**
     * @param array $state
     */
    public function restoreState($state = array())
    {
        if (empty($state)) return;
        foreach ($state as $method => $params) {
            $method = 'set' . ucfirst($method);
            if (method_exists($this, $method)) {
                $this->$method($params);
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "] Error! Not find method: {$method}");
            }
        }
    }

    /**
     * @return array
     */
    public function getState()
    {
        return array(
            'offset' => $this->getOffset(),
            'iteration' => $this->getIteration(),
            'storage' => $this->storage->serialize(),
            'stats' => $this->getStats(),
        );
    }

    /**
     * @return array
     */
    public function prepareReportData()
    {
        return array(
            'stats_total' => $this->getStats(),
            'stats' => $this->getStats(false),
            'iteration' => $this->getIteration(),
            'time' => microtime(true) - $this->time,
            'memory' => (memory_get_usage() - $this->memory) / 1024 / 1024,
            'memory_peak' => (memory_get_peak_usage() - $this->memoryPeak) / 1024 / 1024,
        );
    }

    /**
     * @return bool
     */
    public function saveReport()
    {

        $data = $this->prepareReportData();
        $this->time = microtime(true);
        return $this->task->saveReport($data);
    }

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return $this->msie->getDoUrl() . '?act=download&token=' . $this->task->get('token');
    }

    /**
     * @param string|array $message
     */
    public function debug($message)
    {
        $this->tools->debug($message);
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param string $k
     * @param mixed $default
     * @param bool $skipEmpty
     * @return mixed
     */
    public function getSetting($k, $default = null, $skipEmpty = true)
    {
        $val = array_key_exists($k, $this->settings) ? $this->settings[$k] : $default;
        if ($val === '' && $skipEmpty) {
            $val = $default;
        }
        return $val;

    }

    /**
     * Set a setting value
     *
     * @param string $k
     * @param mixed $v
     * @return void
     */
    public function setSetting($k, $v)
    {
        $this->settings[$k] = $v;
    }


    /**
     * @param string ...$keys
     */
    public function decodeJsonSetting(...$keys)
    {
        foreach ($keys as $key) {
            $setting = $this->getSetting($key, '');
            $setting = $this->tools->fromJSON($setting, array());
            $this->setSetting($key, $setting);
        }
    }

    /**
     * @param string $delimiter
     * @param string ...$keys
     */
    public function explodeAndCleanSetting($delimiter, ...$keys)
    {
        foreach ($keys as $key) {
            $setting = $this->getSetting($key, '');
            $setting = $this->tools->explodeAndClean($setting, $delimiter);
            $this->setSetting($key, $setting);
        }
    }


    /**
     * @param array $properties
     * @return void
     */
    public function setProperties($properties)
    {
        $this->properties = array_merge($this->properties, $properties);
    }

    /**
     * @param string $k
     * @param mixed $default
     * @return mixed
     */
    public function getProperty($k, $default = null)
    {
        return array_key_exists($k, $this->properties) ? $this->properties[$k] : $default;
    }

    /**
     * @param string $k
     * @param mixed $v
     * @return void
     */
    public function setProperty($k, $v)
    {
        $this->properties[$k] = $v;
    }

    /**
     * @param string $key
     * @return void
     */
    public function unsetProperty($key)
    {
        unset($this->properties[$key]);
    }

    /**
     * @return bool
     */
    public function isStop()
    {
        return $this->stopped;
    }

    public function stop()
    {
        $this->stopped = true;
    }

    /**
     * @param int $signal
     */
    protected function signalHandler($signal)
    {
        switch ($signal) {
            case SIGTERM:
                $this->stop();
                break;
            default:
        }
    }
}