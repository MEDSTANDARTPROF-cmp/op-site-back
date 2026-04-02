<?php

abstract class MsIeExportWorker extends MsIeWorker
{

    const DATA_TYPE_KEYS = 'keys';
    const DATA_TYPE_FIELDS = 'fields';
    const DATA_TYPE_RECORDS = 'records';

    /** @var string $classKey */
    protected $classKey = 'modResource';
    /** @var array $query */
    protected $query = array();
    /** @var int */
    protected $total = 0;
    /** @var string $dataType */
    protected $dataType;
    /** @var bool */
    protected $cached = false;
    /** @var bool $isConverValueArr2Str */
    protected $isConverValueArr2Str = true;
    /** @var array $skipConverFieldToJson */
    protected $skipConverFieldArr2Str = array();
    /** @var array $lexicon */
    protected $lexicon = array();
    /** @var array $languageTopics */
    protected $languageTopics = array();
    /** @var null|array $resourceIds */
    protected $resourceIds = null;
    /** @var null|array $tvCaptions */
    protected $tvCaptions = null;


    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->skipConverFieldToJson = $this->prepareSkipConverFieldToJson();
        }
        return $initialized;
    }

    /**
     * @return bool
     */
    public function process()
    {

        $skipTopLines = $this->getSetting('skip_top_lines', 0);
        $addKeys = $this->getSetting('add_keys', 0);
        $addFields = $this->getSetting('add_fields', 0);
        $format = $this->getSetting('export_format', MsIeTools::FILE_TYPE_CSV);
        $this->writer = $this->getWriter($format, $this->getSettings());
        $this->cached = $this->hasCachedFile();
        if ($this->writer) {
            $this->writer->initialize($this->getSettings());
        } else {
            return false;
        }

        if (!$this->beforeStart()) return false;


        if (!$this->cached) {
            $config = $this->buildQueryConfig();
            $config = $this->prepareQueryConfig($config);

            if ($skipTopLines) {
                $this->writer->setOffset($skipTopLines);

            }
            if ($addKeys) {
                $this->dataType = self::DATA_TYPE_KEYS;
                $this->iterate(array($this->getFieldKeys()));
            }

            if ($addFields) {
                $this->dataType = self::DATA_TYPE_FIELDS;
                $this->loadLanguageTopics();
                $this->iterate(array($this->getFieldNames()));
            }

            $idx = 0;
            $this->dataType = self::DATA_TYPE_RECORDS;

            do {
                $idx++;
                $data = $this->executeQuery($config);
                $this->total = (int)$this->modx->getPlaceholder('total');
                $this->iterate($data);
                $config['offset'] = $config['offset'] + $config['limit'];
            } while (
                $this->debug == 0 &&
                $config['limit'] &&
                ceil($this->total / $config['limit']) > $idx
            );
        }
        $this->completed = true;
        return $this->afterFinish();
    }

    /**
     * @return bool
     */
    public function beforeStart()
    {
        $response = $this->fireEvent('msieOnExportStart', array('cached' => $this->cached));
        return is_array($response) ? true : false;
    }

    /**
     * @return bool
     */
    public function afterFinish()
    {
        $ok = true;
        $file = $this->prepareFilePath();
        $archive = $this->getSetting('archive', 0);
        $response = $this->fireEvent('msieOnExportBeforeFinish', array('file' => $file, 'cached' => $this->cached));
        if (is_array($response) && $this->writer) {
            $file = $response['file'];
            if (!$this->cached) {
                $ok = $this->writer->save($file, $this->getSettings());
            }
            if ($ok) {
                if ($archive) {
                    $archiveFile = $this->tools->replaceFileExtension($file, 'zip');
                    if (!$this->cached) {
                        $files = $this->getFilesForArchiving();
                        $files[] = $file;
                        $file = '';
                        $response = $this->fireEvent('msieOnExportBeforeArchive', array('files' => $files));
                        if (is_array($response)) {
                            if ($files = $response['files']) {
                                if ($this->tools->zip($archiveFile, $files)) {
                                    foreach ($files as $val) {
                                        $this->modx->cacheManager->deleteTree($val, array('deleteTop' => true, 'extensions' => ''));
                                    }
                                    $file = $archiveFile;
                                }
                            }
                        }
                    } else {
                        $file = $archiveFile;
                    }
                }
                $this->fireEvent('msieOnExportFinish', array('file' => $file, 'cached' => $this->cached));
                $this->file = $file;
            }
        }
        return parent::afterFinish();
    }

    /**
     * @return array
     */
    public function getFilesForArchiving()
    {
        return array();
    }

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = array(
            'class' => $this->classKey,
            'offset' => 0,
            'limit' => $this->getSetting('limit', 0),
            'where' => $this->tools->fromJSON($this->getSetting('where', ''), array()),
            'leftJoin' => $this->tools->fromJSON($this->getSetting('leftjoin', ''), array()),
            'innerJoin' => $this->tools->fromJSON($this->getSetting('innerjoin', ''), array()),
            'select' => $this->tools->fromJSON($this->getSetting('select', ''), array()),
            'sortby' => $this->getSortBy(),
            'sortdir' => $this->getSortDir(),
            'groupby' => $this->getGroupBy(),
            'fastMode' => false,
            'includeTVs' => array(),
            'tvPrefix' => 'tv.',
        );

        $config['select'][$this->classKey] = $this->modx->getSelectColumns($this->classKey, $this->classKey);
        $config['where'] = $this->prepareValueWhere($config['where']);

        $response = $this->fireEvent('msieOnExportPrepareQuery', array('config' => $config));

        if (is_array($response)) {
            $config = $response['config'];

        }

        $config['setTotal'] = 1;
        $config['totalVar'] = 'total';

        return $config;
    }

    /**
     * @param array $config
     * @return array
     */
    public function prepareQueryConfig(array $config = array())
    {
        return $config;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getSortDir()
    {
        return 'ASC';
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        return $this->classKey . '.id';
    }


    /**
     * @return array
     */
    public function getResourceIds()
    {
        if ($this->resourceIds === null) {
            $resources = array();
            if ($ids = $this->getSetting('resources')) {
                $ctx = $this->getSetting('ctx', 'web');
                $depth = $this->getSetting('search_depth', 10);
                $ids = $this->tools->explodeAndClean($ids);
                foreach ($ids as $id) {
                    $resources[] = (int)$id;
                    $childIds = $this->modx->getChildIds($id, $depth, array('context' => $ctx));
                    $resources = array_merge($resources, $childIds);
                }
            }
            $this->resourceIds = $resources;
        }
        return $this->resourceIds;
    }

    /**
     * @param array $where
     * @return array
     */
    public function prepareValueWhere(array $where = array())
    {
        if ($where) {
            foreach ($where as $key => $val) {
                if (
                    !is_numeric($val) &&
                    !is_array($val) &&
                    substr($val, 0, 1) == ':'
                ) {
                    $code = trim(mb_substr($val, 1));
                    $end = substr($code, -1) == ';' ? '' : ';';
                    $code = 'return ' . $code . $end;
                    $where[$key] = eval($code);
                }
            }
        }
        return $where;
    }

    /**
     * @return array
     */
    public function findTvFields()
    {
        $tvs = array();
        if ($fields = $this->getFields()) {
            foreach ($fields as $field) {
                if (preg_match('/^tv\.(\w+)$/', $field, $matches)) {
                    $tvs[] = $matches[1];
                }
            }
        }
        return $tvs;
    }

    /**
     * @param array $config
     * @return array
     */
    public function executeQuery(array $config = array())
    {
        $pdo = $this->tools->getPdoTools();

        if ($this->debug) {
            $this->debug("Query data:\n\n" . print_r($config, 1) . "\n\n");
            $debugConfig = array_merge($config, array('return' => 'sql'));
            $pdo->setConfig($debugConfig);
            $sql = $pdo->run();
            $this->debug("SQL query:\n\n{$sql}\n\n");
        }

        $config = array_merge($config, array('return' => 'data'));
        $pdo->setConfig($config);
        $data = $pdo->run();
        return $data ? $data : array();
    }


    /**
     * @param array $list
     */
    public function iterate(array $list)
    {
        foreach ($list as $data) {
            if ($this->dataType === self::DATA_TYPE_RECORDS) {
                if ($this->iteration % $this->iterationReport == 0) {
                    $this->saveReport();
                }
                $this->iteration++;
            }
            $response = $this->fireEvent('msieOnExportBeforePrepareRow', array('dataType' => $this->dataType, 'data' => $data));
            if (!is_array($response)) continue;
            $data = $response['data'];
            switch ($this->dataType) {
                case self::DATA_TYPE_KEYS:
                    $data = $this->prepareFieldKeys($data);
                    break;
                case self::DATA_TYPE_FIELDS:
                    $data = $this->prepareFieldNames($data);
                    break;
                default:
                    $data = $this->beforePrepareData($data);
                    $data = $this->prepareData($data);
                    $data = $this->afterPrepareData($data);
            }
            $response = $this->fireEvent('msieOnExportAfterPrepareRow', array('dataType' => $this->dataType, 'data' => $data));
            if (!is_array($response)) continue;
            $data = $response['data'];
            $this->work($data);
            if ($this->debug) {
                break;
            }
        }
    }

    /**
     * @return array
     */
    public function prepareSkipConverFieldToJson()
    {
        return array();
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareFieldKeys(array $data)
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareFieldNames(array $data)
    {
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $result = array();
        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(),1));
        }
        if ($fields = $this->getFields()) {
            foreach ($fields as $field) {
                $val = isset($data[$field]) ? $data[$field] : '';
                if ($methods = $this->getPrepareFieldMethods($field)) {
                    foreach ($methods as $method => $context) {
                        if (method_exists($context, $method)) {
                            $result = $context->$method($field, $data, $result, $this);
                        } else {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, "'{$method}' method not found for preparing the '{$field}' field");
                        }
                    }
                } else {
                    if ($this->isConverValueArr2Str && is_array($val)) {
                        if (in_array($field, $this->skipConverFieldToJson)) {
                            $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                        } else {
                            $val = $this->tools->cleanAndImplode($val, $this->getSetting('first_delimiter', '|'));
                        }
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
        $options = $this->getSettings();
        $response = $this->fireEvent('msieOnExportBeforeWriteRow', array('dataType' => $this->dataType, 'data' => $data, 'options' => $options));
        if (!is_array($response)) return;
        $data = $response['data'];
        $options = $response['options'];
        if (!$this->writer->write($data, $options)) return;
        $this->fireEvent('msieOnExportAfterWriteRow', array('dataType' => $this->dataType, 'data' => $data, 'options' => $options));
    }


    /**
     * @return string
     */
    public function prepareFilePath()
    {
        if (!$path = $this->getSetting('export_path')) {
            $path = $this->tools->getExportPath();
        }

        $path .= DIRECTORY_SEPARATOR;
        $path = $this->tools->preparePath($path, true);

        if (!file_exists($path)) {
            $this->modx->cacheManager->writeTree($path);
        }

        if ($filename = $this->getSetting('filename')) {
            $path .= trim($filename) . '.' . $this->writer->getType();
        } else {
            $path .= date('Y-m-d_h:i:s') . '.' . $this->writer->getType();
        }
        return $path;
    }

    /**
     * @return bool
     */
    public function hasCachedFile()
    {
        $file = $this->prepareFilePath();
        $archive = $this->getSetting('archive', 0);
        $ttl = $this->getSetting('file_ttl', 0);
        $taskTrigger = $this->getProperty('task_trigger', 'backend');
        if ($archive) {
            $file = $this->tools->replaceFileExtension($file, 'zip');
        }
        if (
            $ttl &&
            $taskTrigger == 'frontend' &&
            file_exists($file)
        ) {
            $time = filectime($file);
            if ($time !== false) {
                $diff = time() - $time;
                if ($diff < $ttl) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getFieldKeys()
    {
        return $this->getFields();
    }

    /**
     * @return array
     */
    public function getFieldNames()
    {
        $result = array();
        if ($keys = $this->getFieldKeys()) {
            foreach ($keys as $key) {
                if (isset($this->lexicon[$key])) {
                    $result[] = $this->lexicon[$key];
                } else {
                    if ($this->isTvField($key)) {
                        $tvName = preg_replace('/^(tv\.)(\w+)$/', '$2', $key);
                        $tvCaption = $this->getTvCaption($tvName);
                        if ($tvCaption != $tvName) {
                            $result[] = $tvCaption;
                            continue;
                        }
                    } else if ($this->modx->lexicon->exists('msie_alias_' . $key)) {
                        $result[] = $this->modx->lexicon('msie_alias_' . $key);
                        continue;
                    } else if ($this->modx->lexicon->exists($key)) {
                        $result[] = $this->modx->lexicon($key);
                        continue;
                    }
                    $result[] = $key;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isTvField($field)
    {
        return preg_match('/^tv\.\w+$/', $field) ? true : false;
    }

    /**
     * @return array
     */
    public function getTvCaptions()
    {
        if ($this->tvCaptions === null) {
            $this->tvCaptions = $this->tools->getTvCaptions();
        }
        return $this->tvCaptions;
    }

    /**
     * @param string $tvNama
     * @return string
     */
    public function getTvCaption($tvNama)
    {
        $captions = $this->getTvCaptions();
        return isset($captions[$tvNama]) ? $captions[$tvNama] : $tvNama;
    }


    /**
     * @param string $key
     * @param string $value
     */
    public function addFieldLexicon($key, $value)
    {
        $this->lexicon[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function unsetFieldLexicon($key)
    {
        unset($this->lexicon[$key]);
    }

    public function loadLanguageTopics()
    {
        if ($topics = $this->getLanguageTopics()) {
            foreach ($topics as $topic) {
                $this->modx->lexicon->load($topic);
            }
        }
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return $this->languageTopics;
    }

    /**
     * @param string $topic
     */
    public function addLanguageTopic($topic)
    {
        $this->languageTopics[] = $topic;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function prepareReportData()
    {
        $data = parent::prepareReportData();
        $data['total'] = $this->getTotal();
        if ($this->file) {
            $data['file'] = $this->file;
            $data['cached'] = (int)$this->cached;
            $data['download'] = $this->getDownloadUrl();
        }
        return $data;
    }

    public function stop()
    {
        parent::stop();
        if ($this->writer) {
            $this->writer->close();
        }
    }

}