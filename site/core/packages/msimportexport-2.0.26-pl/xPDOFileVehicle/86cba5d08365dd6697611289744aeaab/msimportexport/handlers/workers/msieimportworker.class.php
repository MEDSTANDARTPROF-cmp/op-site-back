<?php

abstract class MsIeImportWorker extends MsIeWorker
{
    /** @var string $action */
    protected $action;
    /** @var string $workingDirectory */
    protected $workingDirectory;
    /** @var int $startFromLine */
    protected $startFromLine;
    /** @var string $parentClassKey */
    protected $parentClassKey;
    /** @var string $removePrefixField */
    protected $removePrefixField = '';
    /** @var  mixed $checkingValue */
    protected $checkingValue = '';
    /** @var  string $checkingField */
    protected $checkingField = '';

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->startFromLine = $this->getSetting('start_from_line', 0);
            $this->workingDirectory = $this->getSetting('working_directory', '');
            $this->parentClassKey = $this->service->getParentClassKey();
        }
        return $initialized;
    }

    /**
     * @return bool|mixed|void
     */
    public function process()
    {
     //   gc_disable();
        $files = $this->getWorkingDirectoryFiles();
        if ($this->iteration == 0) {
            $this->beforeStart();
            $response = $this->fireEvent('msieOnImportStart', array('files' => $files));
            if (!is_array($response)) return $response;
            $files = $response['files'];
        }
        $settings = $this->getSettings();
        foreach ($files as $file) {
            $this->file = $file;
            if ($this->reader = $this->getReader($file, $settings)) {
                $this->reader->initialize($settings);
                $this->reader->onEvent('read', array($this, 'iterate'));
                if (!$this->reader->open($this->file)) {
                    continue;
                }
                if ($this->getOffset() > 0) {
                    $this->reader->setOffset($this->getOffset() + 1);
                }
                if (!$this->reader->read()) {
                    return false;
                }
            }
            if ($this->isStop()) break;
            $this->setOffset(0);
            if ($this->reader) {
                $this->reader->close();
            }
        }
        $this->completed = !$this->isStop();
        $this->afterFinish();
        return true;
    }

    /**
     * @param array $data
     */
    public function iterate(array $data)
    {
        if ($this->iteration % $this->iterationReport == 0) {
            $this->saveReport();
            //gc_collect_cycles();
        }

        if (!$this->isSkipIterate()) {
            $response = $this->fireEvent('msieOnImportBeforePrepare', array('data' => $data));
            if (!is_array($response)) {
                if ($response === false) {
                    $this->incrStatsRecord('errors');
                }
                return;
            }

            $data = $response['data'];
            $data = $this->beforePrepareData($data);
            $data = $this->prepareData($data);
            $data = $this->afterPrepareData($data);
            $response = $this->fireEvent('msieOnImportAfterPrepare', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data));
            if (!is_array($response)) {
                if ($response === false) {
                    $this->incrStatsRecord('errors');
                }
                return;
            }

            $this->work($response['data']);

            if ($this->debug) {
                $this->stop();
            }
        }

        $this->iteration++;
        $this->setOffset($this->reader->getOffset());
    }

    /**
     * @return bool
     */
    public function isSkipIterate()
    {
        return $this->startFromLine > $this->iteration + 1 ? true : false;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $result = array();
        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(), 1));
        }

        if ($fields = $this->getFields()) {
            foreach ($fields as $index => $field) {
                $val = isset($data[$index]) ? $data[$index] : '';
                if ($methods = $this->getPrepareFieldMethods($field)) {
                    foreach ($methods as $method => $context) {
                        if (method_exists($context, $method)) {
                            $result = $context->$method($field, $val, $data, $result, $this);
                        } else {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, "'{$method}' method not found for preparing the '{$field}' field");
                        }
                    }
                } else {
                    if ($this->removePrefixField) {
                        $field = preg_replace("/^{$this->removePrefixField}/", '', $field);
                    }
                    $result[$field] = $val;
                }
            }
        }
        return $result;
    }

    /**
     * @param array $data
     */
    public function work(array $data)
    {

    }

    /**
     * @param array $data
     * @return bool
     */
    public function afterFinish(array $data = array())
    {
        $ok = parent::afterFinish();
        if ($this->completed) {
            $this->fireEvent('msieOnImportFinish', $data);
        }
        return $ok;
    }


    /**
     * @return array
     */
    public function getWorkingDirectoryFiles()
    {
        $files = array();
        if ($this->workingDirectory) {
            foreach (glob($this->tools->normalizePath($this->workingDirectory . '/*')) as $file) {
                if (is_file($file)) {
                    $files[] = $file;
                }
            }
            if ($files) {
                if ($this->file) {
                    $i = array_search($this->file, $files);
                    if ($i !== false && $i > 0) {
                        $files = array_slice($files, $i);
                    }
                }
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error! Not set working directory.');
        }
        return $files;
    }

    /**
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * @param string $workingDirectory
     */
    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @return array
     */
    public function getState()
    {
        $state = parent::getState();
        $state['file'] = $this->getFile();
        $state['workingDirectory'] = $this->getWorkingDirectory();
        return $state;
    }

    public function stop()
    {
        parent::stop();
        if ($this->reader) {
            $this->reader->stop();
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param string $className
     * @param array $options
     * @param bool $global
     * @return modResource|null
     */
    public function findResource($key, $value, $className = 'modResource', $options = array(), $global = false)
    {
        $ctx = $this->modx->getOption('ctx', $options, '', true);
        $q = $this->modx->newQuery($className);
        $q->select($className . '.id');
        $isProduct = false;
        $value = trim($value);
        if (strtolower($className) == 'msproduct') {
            $q->innerJoin('msProductData', 'Data', $className . '.id = Data.id');
            $isProduct = true;
        }
        $tmp = $this->modx->getFields($className);
        if (array_key_exists($key, $tmp)) {
            $q->where(array($key => $value));
        } elseif ($isProduct) {
            $q->where(array('Data.' . $key => $value));
        }
        if ($ctx && !$global) {
            $q->where(array('context_key:=' => $ctx));
        }
        if ($className != 'modResource') {
            $q->where(array('class_key:=' => $className));
        }

        $q->prepare();

        if ($this->debug) {
            $this->debug($this->modx->lexicon('msimportexport_import_sql_query_find_resource') . "\n" . $q->toSql());
        }

        return $this->modx->getObject($className, $q);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function resourceIdExists($id)
    {
        $result = false;
        $classKey = 'modResource';

        if ($this->storage->hasKeyInStore('resource_id_exists', $id)) {
            return true;
        }

        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id')));
        $q->where(array('id' => $id));
        if ($q->prepare() && $q->stmt->execute()) {
            if ($q->stmt->fetch(PDO::FETCH_COLUMN)) {
                $this->storage->pushStore('resource_id_exists', $id, $id);
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param string $field
     * @param mixed $val
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldCheckingValue($field, $val, array $data, array $result, MsIeWorker &$worker)
    {
        $this->checkingValue = $val;
        return $result;
    }

}