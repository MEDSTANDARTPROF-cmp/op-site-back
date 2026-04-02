<?php

class MsIeTools
{

    const FILE_TYPE_CSV = 'csv';
    const FILE_TYPE_TSV = 'tsv';
    const FILE_TYPE_XLS = 'xls';
    const FILE_TYPE_XLSX = 'xlsx';
    const FILE_TYPE_XLSX2 = 'xlsx2';
    const FILE_TYPE_ODS = 'ods';
    const FILE_TYPE_JSON = 'json';
    const FILE_TYPE_XML = 'xml';
    const FILE_TYPE_PDF = 'pdf';

    /** @var modX $modx */
    public $modx;
    /** @var Msie $msie */
    public $msie;
    /** @var array $config */
    public $config = array();
    /** @var MsIeStorage $storage */
    protected $storage;
    /** @var MsIeReader[] $readers */
    protected $readers = array();
    /** @var MsIeWriter[] $writers */
    protected $writers = array();
    /** @var array $services */
    protected $services = array();
    /** @var array $instances */
    protected $instances = array();
    /** @var array $mediaSourcePath */
    protected $mediaSourcePath = array();

    public function __construct(Msie &$msie, $config = array())
    {
        $this->msie = &$msie;
        $this->modx = &$msie->modx;
        $this->config = array_merge($this->config, $config);
        $this->storage = new MsIeArrStorage();
    }

    /**
     * @param array $config
     * @return pdoTools|null
     */
    public function getPdoTools($config = array())
    {
        if (!$this->hasAddition('pdotools')) return null;
        if (class_exists('pdoFetch') && (!isset($this->instances['pdoFetch']) || !is_object($this->instances['pdoFetch']))) {
            $this->instances['pdoFetch'] = $this->modx->getService('pdoFetch');
            $this->instances['pdoFetch']->setConfig($config);
        }

        return empty($this->instances['pdoFetch']) ? null : $this->instances['pdoFetch'];
    }

    /**
     * @param string $ctx
     * @param array $config
     * @return  miniShop2|null
     */
    public function getMs2Instance($ctx = '', $config = array())
    {
        if (!$this->hasAddition('minishop2')) return null;
        $ctx = $ctx ? $ctx : $this->modx->context->key;
        if (class_exists('miniShop2') && (!isset($this->instances['miniShop2']) || !is_object($this->instances['miniShop2']))) {
            $this->instances['miniShop2'] = $this->modx->getService('miniShop2');
            $this->instances['miniShop2']->initialize($ctx, $config);
        }

        return empty($this->instances['miniShop2']) ? null : $this->instances['miniShop2'];
    }


    /**
     * @param $mode
     * @param array $config
     * @return MsIeService[]
     */
    public function getServices($mode, $config = array())
    {
        $mode = trim($mode);
        $config = array_merge($this->config, $config);

        if (isset($this->services[$mode]) && !empty($this->services[$mode])) {
            return $this->services[$mode];
        }

        $this->services[$mode] = array();
        $path = $this->preparePath($config['servicesPath'], true) . $mode . DIRECTORY_SEPARATOR;

        foreach (glob($path . '*.class.php') as $file) {
            $basename = str_replace('.class.php', '', $this->basename($file));
            if (!$serviceClass = $this->modx->loadClass($basename, $path, true, true)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load service ' . $basename . ' in ' . $path);
            } else {
                $service = new $serviceClass($this, $config);
                if ($service instanceof MsIeService) {
                    $this->services[$mode][$service->getName()] = $service;
                    unset($service);
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Invalid criteria object of class {$serviceClass} encountered.");
                }
            }
        }

        return $this->services[$mode];
    }

    /**
     * @param string $mode
     * @param string $name
     * @return MsIeService|null
     */
    public function getService($mode, $name)
    {
        $mode = trim($mode);
        $name = trim($name);
        $services = $this->getServices($mode);
        return isset($services[$name]) ? $services[$name] : null;
    }

    /**
     * @return array
     */
    public function getSysSettings()
    {
        $settings = $this->getOption('system_settings', null, '[]', true);
        $settings = $this->modx->fromJSON($settings);


        if (empty($settings['php_interpreter'])) {
            $settings['php_interpreter'] = $this->findPathPhpInterpreter();
            //$this->setOption('system_settings', $settings, '', true);
        }

        //  $settings['tmp_path'] = $this->preparePath($settings['tmp_path']);
        //  $settings['upload_path'] = $this->preparePath($settings['upload_path']);

        return $settings ? $settings : array();
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getSysSetting($key, $default = null)
    {
        $settings = $this->getSysSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;

    }

    /**
     * @param string $key
     * @param string|array $value
     * @return bool
     */
    public function setSysSetting($key, $value)
    {
        $settings = $this->getSysSettings();
        $settings[$key] = $value;
        return $this->setOption('system_settings', $settings);
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $namespace
     * @param bool $clearCache
     * @return bool
     */
    public function setOption($key, $value, $namespace = '', $clearCache = false)
    {
        if (empty(trim($key))) return false;

        $namespace = $namespace ? $namespace : $this->msie->getNamespace();
        $key = $namespace . '_' . $key;

        if (!$setting = $this->modx->getObject('modSystemSetting', $key)) {
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->set('namespace', $namespace);
        }

        $val = is_array($value) ? $this->modx->toJSON($value) : $value;
        $setting->set('value', $val);

        if ($setting->save()) {
            $this->modx->setOption($key, $value);
            if ($clearCache) {
                $this->modx->cacheManager->refresh(array('system_settings' => array()));
            }
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @param array $config
     * @param null $default
     * @param bool $skipEmpty
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        $key = $this->msie->getNamespace() . '_' . $key;

        if (!empty($key) and is_string($key)) {
            if ($config != null and array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists($key, $this->modx->config)) {
                $option = $this->modx->getOption($key);
            }
        }
        if ($skipEmpty and empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * @param string $file
     * @param array $options
     *
     * @return MsIeReader|null
     */
    public function getReader(string $file, array $options = array())
    {
        $type = $this->getFileExtension($file);
        $key = $type . '_' . 'reader_class_name';
        $className = $this->modx->getOption($key, $options, '', true);
        /** @var  MsIeReader $reader */
        switch ($type) {
            case self::FILE_TYPE_CSV:
                $reader = $this->getCSVReader($className);
                break;
            case self::FILE_TYPE_TSV:
                $reader = $this->getTSVReader($className);
                break;
            case self::FILE_TYPE_XLS:
            case self::FILE_TYPE_XLSX:
                $reader = $this->getXLSXReader($className);
                break;
            case self::FILE_TYPE_ODS:
                $reader = $this->getODSXReader($className);
                break;
            case self::FILE_TYPE_JSON:
                $reader = $this->getJSONReader($className);
                break;
            case self::FILE_TYPE_XML:
                $reader = $this->getXMLReader($className);
                break;
            default:
                $reader = null;
        }

        if (empty($reader)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error! Not find reader for file: {$file}");
        }
        return $reader;
    }

    /**
     * @param string $className
     *
     * @return MsIeCSVReader
     */
    public function getCSVReader(string $className = '')
    {
        $className = $className ?: $this->getReaderClass(self::FILE_TYPE_CSV);
        return $this->loadReader($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeTSVReader
     */
    public function getTSVReader(string $className = '')
    {
        $className = $className ?: $this->getReaderClass(self::FILE_TYPE_TSV) ?: 'MsIeTSVReader';
        return $this->loadReader($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeXLSXReader
     */
    public function getXLSXReader(string $className = '')
    {
        $className = $className ?: $this->getReaderClass(self::FILE_TYPE_XLSX);
        return $this->loadReader($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeODSReader
     */
    public function getODSXReader(string $className = '')
    {
        $className = $className ?: $this->getReaderClass(self::FILE_TYPE_ODS);
        return $this->loadReader($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeJSONReader
     */
    public function getJSONReader(string $className = '')
    {
        $className = $className ?: $this->getReaderClass(self::FILE_TYPE_JSON);
        return $this->loadReader($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeXMLReader
     */
    public function getXMLReader(string $className = '')
    {
        $className = $className ?: $this->getReaderClass(self::FILE_TYPE_XML);
        return $this->loadReader($className);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getReaderClass(string $key)
    {
        $classes = $this->modx->fromJSON($this->getOption('readers'));
        return $classes[$key] ?? '';

    }

    /**
     * @param string $className
     * @param string $path An optional path to start the search from.
     * @param array $config
     *
     * @return null|MsIeReader
     */
    public function loadReader(string $className, string $path = '', array $config = array())
    {
        $config = array_merge($this->msie->config, $config);
        $path = empty($path) ? $config['readersPath'] : $path;

        if (
            !isset($this->readers[$className]) ||
            !is_object($this->readers[$className]) ||
            !($this->readers[$className] instanceof MsIeReader)
        ) {
            $class = $this->modx->loadClass($className, $path, true, true);
            if ($class) {
                $this->readers[$className] = new $class($this->modx, $config);
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Error load reader. Class to load: {$className}. Path: {$path}");
            }
        }
        return empty($this->readers[$className]) ? null : $this->readers[$className];
    }

    /**
     * @param string $type
     * @param array $options
     *
     * @return MsIeWriter|null
     */
    public function getWriter(string $type, array $options = array())
    {
        $key = $type . '_' . 'writer_class_name';
        $className = $this->modx->getOption($key, $options, '', true);
        /** @var  MsIeWriter $writer */
        switch ($type) {
            case self::FILE_TYPE_CSV:
                $writer = $this->getCSVWriter($className);
                break;
            case self::FILE_TYPE_TSV:
                $writer = $this->getTSVWriter($className);
                break;
            case self::FILE_TYPE_XLS:
            case self::FILE_TYPE_XLSX:
                $writer = $this->getXLSXWriter($className);
                break;
            case self::FILE_TYPE_XLSX2:
                $writer = $this->getXLSX2Writer($className);
                break;
            case self::FILE_TYPE_ODS:
                $writer = $this->getODSWriter($className);
                break;
            case self::FILE_TYPE_JSON:
                $writer = $this->getJSONWriter($className);
                break;
            case self::FILE_TYPE_XML:
                $writer = $this->getXMLWriter($className);
                break;
            default:
                $writer = null;
        }

        if (empty($writer)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error! Not find writer for: {$type}");
        }
        return $writer;
    }

    /**
     * @param string $className
     *
     * @return MsIeCSVWriter
     */
    public function getCSVWriter(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_CSV);
        return $this->loadWriter($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeTSVWriter
     */
    public function getTSVWriter(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_TSV) ?: 'MsIeTSVWriter';
        return $this->loadWriter($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeXLSXWriter
     */
    public function getXLSXWriter(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_XLSX);
        return $this->loadWriter($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeXLSX2Writer
     */
    public function getXLSX2Writer(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_XLSX2);
        return $this->loadWriter($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeODSWriter
     */
    public function getODSWriter(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_ODS);
        return $this->loadWriter($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeJSONWriter
     */
    public function getJSONWriter(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_JSON);
        return $this->loadWriter($className);
    }

    /**
     * @param string $className
     *
     * @return MsIeXMLWriter
     */
    public function getXMLWriter(string $className = '')
    {
        $className = $className ?: $this->getWriterClass(self::FILE_TYPE_XML);
        return $this->loadWriter($className);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getWriterClass(string $key)
    {
        $classes = $this->modx->fromJSON($this->getOption('writers'));
        return isset($classes[$key]) ? $classes[$key] : '';

    }

    /**
     * @param string $className
     * @param string $path An optional path to start the search from.
     * @param array $config
     * @return null|MsIeWriter
     */
    public function loadWriter($className, $path = '', $config = array())
    {
        $config = array_merge($this->msie->config, $config);
        $path = empty($path) ? $config['writersPath'] : $path;
        if (
            !isset($this->writers[$className]) ||
            !is_object($this->writers[$className]) ||
            !($this->writers[$className] instanceof MsIeWriterInterface)
        ) {
            $class = $this->modx->loadClass($className, $path, true, true);
            if ($class) {
                $this->writers[$className] = new $class($this->modx, $config);
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Error load writer. Class to load: {$className}. Path: {$path}");
            }
        }
        return empty($this->writers[$className]) ? null : $this->writers[$className];
    }

    /**
     * @return array
     */
    public function getAllowedReaderExtensions()
    {
        return MsIeReader::getExtensions();
    }

    /**
     * @param array $exclude
     * @return array
     */
    public function getKeysContexts(array $exclude = array('mgr'))
    {
        $keys = array();
        $classKey = 'modContext';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('key')));
        if ($exclude) {
            $q->where(array('key:NOT IN' => $exclude));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            $keys = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $keys;
    }

    /**
     * @param string|int $source
     * @param string $ctx
     * @return string
     */
    public function getPathByMediaSource($source, string $ctx = 'web')
    {
        $path = '';
        $key = $source . $ctx;
        if (isset($this->mediaSourcePath[$key])) {
            $path = $this->mediaSourcePath[$key];
        } else if ($mediaSource = $this->modx->getObject('sources.modMediaSource', $source)) {
            /** @var modMediaSource $mediaSource */
            $mediaSource->set('ctx', $ctx);
            $mediaSource->initialize();
            $path = $mediaSource->getBasePath();
            $this->mediaSourcePath[$key] = $path;
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Media source not found. ID:' . $source);
        }
        return $path;
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getResourceFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $this->modx->lexicon->load('resource');
        $aFields = array_keys($this->modx->getFields('modResource'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'resource_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {

                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'resource_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'resource_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getResourceCustomFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();

        $aFields = array('parent_pagetitle', 'parents', 'href');

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'msie_alias_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }


    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getProductFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();

        if (!$this->hasAddition('minishop2')) return $list;

        $this->modx->lexicon->load('minishop2:product');
        $aFields = array_keys($this->modx->getFields('msProductData'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'ms2_product_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getProductCustomFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();

        if (!$this->hasAddition('minishop2')) return $list;

        $aFields = array('categories', 'vendor_name', 'vendor_country', 'url_thumb', 'url_image', 'attach_thumb');

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'msie_alias_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }


    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getProductPluginsFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $aFields = array();

        if (!$this->hasAddition('minishop2')) return $list;

        $this->modx->lexicon->load('minishop2:product');
        $plugins = (array)$this->getMs2Instance()->loadPlugins();

        foreach ($plugins as $field => $plugin) {
            if (!empty($plugin['xpdo_meta_map']['msProductData'])) {
                if ($arr = $plugin['xpdo_meta_map']['msProductData']['fields']) {
                    $aFields = array_merge($aFields, $arr);
                }
            }
        }

        $aFields = array_keys($aFields);

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'ms2_product_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }


    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getProductOptionsFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $options = array();

        if (!$this->hasAddition('minishop2')) return $list;

        $q = $this->modx->newQuery('msOption');
        $q->select($this->modx->getSelectColumns('msOption', 'msOption', '', array('key', 'caption')));

        if ($q->prepare() && $q->stmt->execute()) {
            while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $options[$item['key']] = $item['caption'];
            }
        }

        $aFields = array_keys($options);

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $options[$field],
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $options[$field],
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $options[$field],
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /**
     * @param string $field
     * @return null|string
     */
    public function getProductFieldType($field)
    {
        $meta = $this->modx->getFieldMeta('msProductData');
        if (isset($meta[$field])) return $meta[$field]['phptype'];
        return null;

    }

    /**
     * @param string $field
     * @return null|string
     */
    public function getProductOptionFieldType($field)
    {
        $field = str_replace('options-', '', $field);
        $meta = $this->getProductOptionFieldMeta();
        if (isset($meta[$field])) return $meta[$field]['type'];
        return null;
    }

    /**
     * @return array
     */
    public function getProductOptionFieldMeta()
    {
        $classKey = 'msOption';
        $meta = $this->storage->getStore('option_field_meta', null);
        if ($meta === null) {
            $meta = array();
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $meta[$item['key']] = $item;
                }
                $this->storage->setStore('option_field_meta', $meta);
            }
        }
        return $meta;
    }


    /* @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getSeoProFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        if (!$this->hasAddition('seopro')) return $list;
        $this->modx->lexicon->load('seopro:default');
        $field = 'keywords';

        $key = $prefixKey . $field;
        $list[$key] = array(
            'key' => $key,
            'name' => $field,
            'alias' => $this->lexicon($field, 'seopro.'),
            'label' => $label,
        );
        return $list;
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param string $keyName
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getTvFields($prefixKey = '', $label = '', $keyName = 'id', $fields = array(), $exclude = false)
    {
        $list = array();
        if ($tvs = $this->getTVs($fields, $exclude)) {
            foreach ($tvs as $tv) {
                $key = $prefixKey . $tv[$keyName];
                $list[$key] = array(
                    'key' => $key,
                    'id' => $tv['id'],
                    'name' => $tv['name'],
                    'alias' => !empty($tv['caption']) ? $tv['caption'] : '',
                    'label' => $label,

                );
            }
        }
        return $list;
    }

    public function getTVs($tvs = array(), $exclude = false)
    {
        $result = array();
        $q = $this->modx->newQuery('modTemplateVar');
        $q->select(array(
            'modTemplateVar.*',
        ));
        if (!empty($tvs)) {
            if ($exclude) {
                $q->where(array(
                    'name:NOT IN' => $tvs
                ));
            } else {
                $q->where(array(
                    'name:IN' => $tvs
                ));
            }
        }
        if ($q->prepare() && $q->stmt->execute()) {
            while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$item['id']] = $item;
            }
        }
        return $result;
    }

    /**
     * @param string $indexKey
     * @param array $tvs
     * @param false $exclude
     * @return array
     */
    public function getTvCaptions($indexKey = 'name', $tvs = array(), $exclude = false)
    {
        $result = array();
        $q = $this->modx->newQuery('modTemplateVar');
        $q->select(array(
            'modTemplateVar.*',
        ));
        if (!empty($tvs)) {
            if ($exclude) {
                $q->where(array(
                    'name:NOT IN' => $tvs
                ));
            } else {
                $q->where(array(
                    'name:IN' => $tvs
                ));
            }
        }
        if ($q->prepare() && $q->stmt->execute()) {
            while ($tv = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$tv[$indexKey]] = empty($tv['caption']) ? $tv['name'] : $tv['caption'];
            }
        }
        return $result;
    }

    /**
     * @param string $key
     * @param string $prefix
     * @param bool $repeat
     * @return string
     */
    public function lexicon($key, $prefix = '', $repeat = true)
    {
        $pkey = $prefix . $key;
        $str = $this->modx->lexicon($pkey);
        if ($str != $pkey) {
            return $str;
        } else if ($repeat) {
            return $this->lexicon($key, 'ms2_product_', false);
        }
        return $key;
    }


    /**
     * @param string $key
     * @param string $value
     * @param string $topic
     * @param array $options
     *
     * @return bool
     */
    public function addLexicon($key, $value, $topic, $options = array())
    {
        $clearCache = $options['clearCache'] ?? false;
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('workspace/lexicon/create', array(
            'name' => $key,
            'value' => $value,
            'topic' => $topic,
            'namespace' => $options['namespace'] ?? 'msimportexport',
            'language' => $options['language'] ?? $this->modx->getOption('manager_language'),
        ));
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response->getMessage(), 1));
            return false;
        }
        if ($clearCache) {
            $this->modx->lexicon->clearCache();
        }
        return true;
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $topic
     * @param array $options
     *
     * @return bool
     */
    public function updateLexicon($key, $value, $topic, $options = array())
    {
        $data = array(
            'name' => $key,
            'value' => $value,
            'topic' => $topic,
            'namespace' => $options['namespace'] ?? 'msimportexport',
            'language' => $options['language'] ?? $this->modx->getOption('manager_language'),
        );

        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('workspace/lexicon/updatefromgrid', array(
            'data' => $this->modx->toJSON($data)
        ));
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response->getMessage(), 1));
            return false;
        }
        return true;
    }

    /**
     * @param string $key
     * @param string $topic
     * @param array $options
     *
     * @return bool
     */
    public function removeLexicon($key, $topic, $options = array())
    {
        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('workspace/lexicon/revert', array(
            'name' => $key,
            'topic' => $topic,
            'namespace' => $options['namespace'] ?? 'msimportexport',
            'language' => $options['language'] ?? $this->modx->getOption('manager_language'),
        ));
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response->getMessage(), 1));
            return false;
        }
        return true;
    }


    /**
     * @param string|array $paths
     * @param array $options
     * @return bool|array
     */
    public function gc($paths = '', $options = array('deleteTop' => false, 'skipDirs' => false))
    {
        $result = false;
        $paths = $paths ? $paths : array(
            $this->getTmpPath(),
            $this->getUploadPath(),
        );
        $paths = is_array($paths) ? $paths : array($paths);
        $maxLifeTime = $this->modx->getOption('maxLifeTime', $options, $this->getSysSetting('gc_file_maxlifetime'));
        if (empty($maxLifeTime)) return;
        if (!$paths) return $result;
        $result = array();
        foreach ($paths as $path) {
            foreach (glob($this->normalizePath($path . '/*')) as $file) {
                if (is_dir($file)) {
                    $suboptions = array_merge($options, array('deleteTop' => !$this->modx->getOption('skipDirs', $options, false)));
                    if ($subresult = $this->gc($file, $suboptions)) {
                        $result = array_merge($result, $subresult);
                    }
                } else {
                    if ((time() - filectime($file)) > $maxLifeTime) {
                        if (unlink($file)) {
                            array_push($result, $file);
                        }
                    }
                }
            }
            if ($this->modx->getOption('deleteTop', $options, false)) {
                if (@rmdir($path)) {
                    array_push($result, $path);
                }
            }
        }
        return $result;
    }

    /**
     * @param string $file
     * @param array $options
     * @return bool|string
     */
    public function upload($file, $options = array())
    {

        $path = $this->normalizePath($this->modx->getOption('path', $options, $this->getUploadPath()));
        if ($this->modx->getOption('mkdir', $options, false)) {
            do {
                $newPath = $path . uniqid() . DIRECTORY_SEPARATOR;
            } while (file_exists($newPath));
            $path = $newPath;
        }

        if (!file_exists($path)) {
            if (!$this->modx->cacheManager->writeTree($path)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error create path:' . $path);
                return false;
            }
        }

        if ($this->isUrl($file)) {
            if (!$file = $this->download($file, $path)) {
                return false;
            }
        } else if (is_uploaded_file($file)) {
            if (!$tmpFile = $this->getUploadedFileByTmpName($file)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error get uploaded file by tmp name:' . $file);
                return false;
            }
            $file = $path . $tmpFile['name'];
            if (!move_uploaded_file($tmpFile['tmp_name'], $file)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error move uploaded file to:' . $file);
                return false;
            }
        } else {
            $newFile = $path . $this->basename($file);
            if (!$this->modx->cacheManager->copyFile($file, $newFile)) {
                return false;
            }
            if ($this->modx->getOption('remove_source_file', $options, 0)) {
                unlink($file);
            }
            $file = $newFile;
        }
        return $file;
    }

    /**
     * @param string $name
     * @return bool|array
     */
    public function getUploadedFileByTmpName($name)
    {
        if (empty($_FILES)) return false;
        foreach ($_FILES as $file) {
            if ($file['tmp_name'] == $name) {
                return $file;
            }
        }
        return false;
    }

    /**
     * @param string $path
     * @param array $options
     * @return array
     */
    public function findFiles($path, $options = array())
    {

        $result = array();
        $masks = $this->modx->getOption('masks', $options, array());
        if (is_string($masks)) {
            $masks = explode(',', $masks);
        }
        $masks = array_map('strtolower', $masks);
        $exclude = $this->modx->getOption('exclude', $options, array());
        if (is_string($exclude)) {
            $exclude = explode(',', $exclude);
        }
        $exclude = array_map('strtolower', $exclude);
        $maxDepth = $this->modx->getOption('maxDepth', $options, 0);
        $depth = $this->modx->getOption('depth', $options, 1);
        $sorted = $this->modx->getOption('sorted', $options, false);
        foreach (glob($this->normalizePath($path . '/*')) as $file) {
            if (is_dir($file)) {
                if (empty($maxDepth) || $depth < $maxDepth) {
                    $depth++;
                    $suboptions = array_merge($options, array('depth' => $depth));
                    $subresult = $this->findFiles($file, $suboptions);
                    $result = array_merge($result, $subresult);
                }
            } else {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ((empty($masks) || (in_array($ext, $masks)) && !in_array($ext, $exclude))) {
                    $result[] = $file;
                }
            }
        }
        if ($sorted) {
            sort($result, SORT_NATURAL);
        }
        return $result;
    }

    /**
     * @param string $url
     * @param string $path
     *
     * @return bool|string
     */
    public function download(string $url, string $path = '')
    {
        $fp = null;
        $file = '';
        $self = $this;
        $headers = array();
        $url = $this->normalizeUrl($url);
        $path = $path ? trim($path) : $this->getTmpPath();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        //curl_setopt($ch, CURLOPT_REFERER, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6',
            )
        );
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function ($ch, $string) use (&$headers) {
                $len = strlen($string);
                $header = explode(':', $string, 2);
                if (count($header) < 2) return $len;
                $name = strtolower(trim($header[0]));
                $headers[$name] = trim($header[1]);
                return $len;
            });
        curl_setopt($ch, CURLOPT_WRITEFUNCTION,
            function ($ch, $string) use (&$self, &$fp, &$file, &$headers, $url, $path) {
                if ($fp === null) {
                    $basename = '';
                    if (isset($headers['content-disposition'])) {
                        $parts = explode(';', $headers['content-disposition']);
                        foreach ($parts as $crumb) {
                            if (strstr($crumb, '=')) {
                                list($pname, $pval) = explode('=', $crumb);
                                $pname = trim($pname);
                                if (strcasecmp($pname, 'filename') == 0) {
                                    $basename = $this->basename($self->unquote($pval));
                                }
                            }
                        }
                    }
                    if (empty($basename)) {
                        $info = pathinfo($url);
                        $ext = isset($info['extension']) ? $info['extension'] : '';
                        $filename = isset($info['filename']) ? $info['filename'] : uniqid('file_');
                        $contentType = strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
                        if ($contentType && $mimeTypes = $self->getMimeTypes()) {
                            $ext = isset($mimeTypes[$contentType]) ? $mimeTypes[$contentType] : $info['extension'];
                        }
                        
                        $filename = str_replace('%20', '_', $filename);
                        $basename = $filename . '.' . $ext;
                    }
                    $file = $path . $basename;
                    if (!$fp = @fopen($file, 'wb')) {
                        throw new Exception("Can't open file: {$file} for writing");
                    }
                }
                $len = fwrite($fp, $string);
                return $len;
            });

        try {
            if (!$output = curl_exec($ch)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Error download. Url : {$url}. Message:\n" . curl_error($ch) . "\nError:\n" . curl_errno($ch));
            }
        } catch (Exception $e) {
            $file = '';
            $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
        }
        if ($fp) fclose($fp);
        curl_close($ch);
        return $file ? $file : false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function browserDownload($file)
    {
        if (file_exists($file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $this->basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            if (readfile($file) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $param
     * @param null $suffix
     * @return string
     */
    public function basename($param, $suffix = null)
    {
        $charset = $this->modx->getOption('modx_charset', null, 'utf-8');
        if ($suffix) {
            $tmpstr = ltrim(mb_substr($param, mb_strrpos($param, DIRECTORY_SEPARATOR, null, $charset), null, $charset), DIRECTORY_SEPARATOR);
            if ((mb_strpos($param, $suffix, null, $charset) + mb_strlen($suffix, $charset)) == mb_strlen($param, $charset)) {
                return str_ireplace($suffix, '', $tmpstr);
            } else {
                return ltrim(mb_substr($param, mb_strrpos($param, DIRECTORY_SEPARATOR, null, $charset), null, $charset), DIRECTORY_SEPARATOR);
            }
        } else {
            return ltrim(mb_substr($param, mb_strrpos($param, DIRECTORY_SEPARATOR, null, $charset), null, $charset), DIRECTORY_SEPARATOR);
        }
    }

    /**
     * @param bool $slash
     * @return string
     */
    public function getSiteUrl($slash = false)
    {
        return $this->msie->getSiteUrl($slash);
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isUrl($url)
    {
        $url = parse_url(trim($url));
        return isset($url['host']);
    }

    /**
     * @param string $file
     * @return false
     */
    public function isFileFromWorkingDirectory($file)
    {
        return preg_match('/^\.\//', $file) ? true : false;
    }

    /**
     * @param string $url
     * @return bool
     */
    function remoteFileExists($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6',
            )
        );
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code == 200 ? true : false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function fileExists($file)
    {
        if ($this->isUrl($file)) {
            return $this->remoteFileExists($file);
        } else {
            return file_exists($file);
        }
    }

    /**
     * @param string $file
     * @return string
     */
    public function getFileExtension($file)
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * @param string $file
     * @param string $ext
     * @return string
     */
    public function replaceFileExtension($file, $ext)
    {
        $tmp = pathinfo($file);
        return $tmp['dirname'] . DIRECTORY_SEPARATOR . $tmp['filename'] . '.' . $ext;
    }

    /**
     * @param string $file
     * @return null|string
     */
    public function detectFileMimeType($file)
    {
        $type = null;
        if (function_exists('finfo_open')) {
            if ($fileInfoDb = finfo_open(FILEINFO_MIME)) {
                $type = finfo_file($fileInfoDb, $file);
            }
        } elseif (function_exists('mime_content_type')) {
            $type = mime_content_type($file);
        }
        return $type;
    }

    /**
     * @return array
     */
    public function getMimeTypes()
    {
        $cacheKey = $this->getCacheKey('getMimeTypes');
        $url = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
        if (!$mimes = $this->modx->cacheManager->get($cacheKey)) {
            $mimes = array();
            foreach (@explode("\n", @file_get_contents($url)) as $x) {
                if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1) {
                    $mimes[$out[1][0]] = $out[1][1];
                    /*for ($i = 1; $i < $c; $i++) {
                        //$mimes[$out[1][$i]] = $out[1][0];

                    }*/
                }
            }
            $this->modx->cacheManager->set($cacheKey, $mimes, $this->config['cacheTime']);
        }
        return $mimes;
    }

    /**
     * @param string $file
     * @param string|array $source
     * @param array $options
     * @return bool
     */
    public function zip($file, $source, array $options = array())
    {
        $packed = false;
        if (
            class_exists('ZipArchive', true) &&
            $this->modx->loadClass('compression.xPDOZip', XPDO_CORE_PATH, true, true)
        ) {
            $archive = new xPDOZip($this->modx, $file, array(xPDOZip::CREATE => true, xPDOZip::OVERWRITE => true));
            if ($archive) {
                if (is_string($source)) {
                    $source = array($source);
                }
                $baseTarget = pathinfo($file, PATHINFO_FILENAME);
                $fullTarget = $this->modx->getOption('full_target', $options, false, true);

                foreach ($source as $val) {
                    if ($fullTarget) {
                        $target = pathinfo($val, PATHINFO_DIRNAME);
                    } else {
                        $target = $baseTarget;
                        if (is_dir($val)) {
                            $target = $baseTarget . '/' . pathinfo($val, PATHINFO_FILENAME);
                        }
                    }
                    $archive->pack($val, array(xPDOZip::ZIP_TARGET => "{$target}/"));
                    if (!$archive->hasError()) {
                        $packed = true;
                    } else {
                        $packed = false;
                        $errors = $archive->getErrors();
                        $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Error packing {$source} to {$file}: " . print_r($errors, true));
                        return $packed;
                    }
                }
            }
        }
        return $packed;
    }

    /**
     * @param string $file
     * @param string $path
     *
     * @return array|bool
     */

    public function unzip(string $file, string $path)
    {
        if (!class_exists('xPDOTransport')) {
            $this->modx->loadClass('transport.xPDOTransport', XPDO_CORE_PATH, true, true);
        }
        return xPDOTransport::_unpack($this->modx, $file, $path);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isZipArchive(string $file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mimeType === 'application/zip' ? true : false;
    }

    /**
     * @param string $file
     * @param string $sourceEncode
     * @return bool
     * @throws Exception
     */
    public function convertFileToUTF8($file, $sourceEncode = '')
    {
        $sourceEncode = empty($sourceEncode) ? $this->detectFileEncoding($file) : $sourceEncode;

        if (strtolower($sourceEncode) == 'utf-8') return true;

        $content = file_get_contents($file);

        if ($content = $this->convertEncoding($content, $sourceEncode, 'UTF-8')) {
            return file_put_contents($file, $content) ? true : false;
        }
        return false;
    }

    /**
     * @param string $file
     * @return string
     * @throws Exception
     */
    public function detectFileEncoding($file)
    {
        $content = file_get_contents($file);
        $encode = mb_detect_encoding($content, 'auto', true);
        if (!$encode) {
            throw new Exception("Unable to correctly determine the file encoding {$file}");
        }
        return $encode;
    }

    /**
     * convert the given string to the given encoding.
     *
     * @param string $value string to be converted
     * @param string $sourceEncoding The encoding used to encode the source string
     * @param string $targetEncoding The encoding the string should be re-encoded into
     * @return string The converted string, encoded with the given encoding
     * @throws Exception
     */
    public function convertEncoding($value, $sourceEncoding, $targetEncoding)
    {
        if ($this->isIconvEnabled()) {
            $convertedValue = iconv($sourceEncoding, $targetEncoding, $value);
        } else if ($this->isMbStringEnabled()) {
            $convertedValue = mb_convert_encoding($value, $targetEncoding, $sourceEncoding);
        } else {
            throw new Exception("The conversion from $sourceEncoding to $targetEncoding is not supported. Please install \"iconv\" or \"PHP Intl\".");
        }
        if ($convertedValue === false) {
            throw new Exception("The conversion from $sourceEncoding to $targetEncoding failed.");
        }
        return $convertedValue;
    }

    /**
     * Check if a string contains UTF8 data.
     *
     * @param string $value
     * @return bool
     */
    public function isUTF8($value)
    {
        return $value === '' || preg_match('/^./su', $value) === 1;
    }

    /**
     * Try to sanitize UTF8, stripping invalid byte sequences. Not perfect. Does not surrogate characters.
     *
     * @param string $value
     * @return string
     */
    public function sanitizeUTF8($value)
    {
        if ($this->isIconvEnabled()) {
            $value = @iconv('UTF-8', 'UTF-8', $value);
            return $value;
        }
        return $value;
    }

    /**
     * Get whether iconv extension is available.
     *
     * @return bool
     */
    public function isIconvEnabled()
    {
        return function_exists('iconv');
    }

    /**
     * Returns whether "mb_string" functions can be used.
     * These functions come with the PHP Intl package.
     *
     * @return bool TRUE if "mb_string" functions are available and can be used, FALSE otherwise
     */
    public function isMbStringEnabled()
    {
        return function_exists('mb_convert_encoding');
    }

    /**
     * @return string
     */
    public function getTmpPath()
    {
        $path = $this->getSysSetting('tmp_path', '{+assets_path}tmp' . DIRECTORY_SEPARATOR);
        $path = $this->preparePath($path);
        if (!file_exists($path)) {
            $this->modx->cacheManager->writeTree($path);
        }
        return $path;
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        $path = $this->getSysSetting('upload_path', '{+assets_path}upload' . DIRECTORY_SEPARATOR);
        $path = $this->preparePath($path);
        if (!file_exists($path)) {
            $this->modx->cacheManager->writeTree($path);
        }
        return $path;
    }

    /**
     * @return string
     */
    public function getExportPath()
    {
        $path = $this->getSysSetting('export_path', '{+assets_path}export' . DIRECTORY_SEPARATOR);
        $path = $this->preparePath($path);
        if (!file_exists($path)) {
            $this->modx->cacheManager->writeTree($path);
        }
        return $path;
    }

    /**
     * @param $str
     * @param string $default
     * @param bool $skipEmpty
     * @return mixed|string
     */
    public function fromJSON($str, $default = '', $skipEmpty = true)
    {
        $val = $this->modx->fromJSON($str);
        if (($val === '' || $val === null) && $skipEmpty) {
            $val = $default;
        }
        return $val;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    function fixUtf8(string $content)
    {
        return preg_replace('/[\x00-\x1F\x7F]/', '', $content);
        /* return preg_replace_callback('#[\\xA1-\\xFF](?![\\x80-\\xBF]{2,})#', function ($m) {
             return utf8_encode($m[0]);
         }, $content);
         return iconv('UTF-8', 'UTF-8//IGNORE', $content);*/
    }

    /**
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param $eventName
     * @param array $params
     * @param $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return array(
            'success' => empty($message),
            'message' => $message,
            'data' => $params,
        );
    }

    /**
     * @return string
     */
    public function findPathPhpInterpreter()
    {
        if ($this->checkFunctionEnabled('exec')) {
            return exec('which php');
        } else {
            return PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function checkFunctionEnabled($name)
    {
        $f = array_map('trim', explode(', ', ini_get('disable_functions')));
        if (
            function_exists($name) &&
            !in_array($name, $f) &&
            !in_array(strtolower(ini_get('safe_mode')), array('on', '1'), true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function checkDaemonMode()
    {
        return $this->getSysSetting('daemon_mode', false);
    }

    /**
     * @return bool
     */
    public function checkPcntlSignal()
    {
        $ok = false;
        if ($this->checkFunctionEnabled('pcntl_signal') && $this->checkExec() && $this->checkPhpInterpreter()) {
            $php = $this->getSysSetting('php_interpreter');
            $code = 'echo (function_exists(\'pcntl_signal\') && !in_array(\'pcntl_signal\', array_map(\'trim\', explode(\', \', ini_get(\'disable_functions\'))))) ? 1 : 0;';
            $out = exec($php . ' -r "' . $code . '"');
            $ok = $out == 1 ? true : false;
        }
        return $ok;

    }

    /**
     * @return bool
     */
    public function checkPhpVersionCli()
    {
        $ok = false;
        if ($this->checkExec() && $this->checkPhpInterpreter()) {
            $minVersion = $this->getOption('min_php_version', null, '5.6.*');
            if ($curVersion = $this->getPhpVersionCli()) {
                $ok = version_compare($minVersion, $curVersion) <= 0 ? true : false;
            }
        }
        return $ok;

    }

    /**
     * @return bool
     */
    public function checkPhpVersionSite()
    {
        $ok = false;
        if ($curVersion = $this->getPhpVersionSite()) {
            $minVersion = $this->getOption('min_php_version', null, '5.6.*');
            $ok = version_compare($minVersion, $curVersion) <= 0 ? true : false;
        }
        return $ok;

    }

    /**
     * @return bool
     */
    public function checkExec()
    {
        return $this->checkFunctionEnabled('exec');
    }

    /**
     * @param bool $parse
     * @return string
     */
    public function getPhpVersionSite($parse = true)
    {
        $v = phpversion();
        return $parse ? $this->parsePhpVersion($v) : $v;

    }

    /**
     * @param bool $parse
     * @return string
     */
    public function getPhpVersionCli($parse = true)
    {
        $php = $this->getSysSetting('php_interpreter');
        $v = exec($php . ' -r "echo phpversion(); "');
        return $parse ? $this->parsePhpVersion($v) : $v;

    }

    /**
     * @return bool
     */
    public function checkPhpInterpreter()
    {
        $ok = false;
        if ($php = $this->getSysSetting('php_interpreter') && $this->checkExec()) {
            $ok = $this->getPhpVersionCli(false) ? true : false;
        }
        return $ok;

    }

    /**
     * @param string $v
     * @return string
     */
    public function parsePhpVersion($v)
    {
        preg_match("#^\d+(\.\d+)*#", $v, $match);
        return isset($match[0]) ? $match[0] : '';
    }

    /**
     * @param array|string $options
     * @return string
     */
    public function getCacheKey($options)
    {
        return $this->msie->getNamespace() . DIRECTORY_SEPARATOR . sha1(is_array($options) ? serialize($options) : $options);
    }

    /**
     * @param array $params
     * @return string
     */
    public function generateSign($params = array())
    {
        $sign = '';
        ksort($params);
        foreach ($params as $key => $value) {
            $sign .= $key . '=' . $value;
        }
        return md5($sign);
    }

    /**
     * Sanitize the specified path
     *
     * @param string $path The path to clean
     * @return string The sanitized path
     */
    public function normalizePath($path)
    {
        $path = str_replace('./', '/', $path);
        return preg_replace(array("/\.*[\/|\\\]/i", "/[\/|\\\]+/i"), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $path);
    }


    public function normalizeUrl(string $url): string
    {
        return str_replace(' ', '%20', trim($url));
    }

    /**
     * @param string $str
     * @return mixed
     */
    public function unquote($str)
    {
        return str_replace(array("'", '"'), '', trim($str));
    }

    /**
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function uniqid($length = 32)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes($length);
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes($length);
        } else {
            return substr(sha1(uniqid(rand(), true)), 0, $length);
        }
        return substr(bin2hex($bytes), 0, $length);
    }

    /**
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public function explodeAndClean($str, $delimiter = ',')
    {
        $array = explode($delimiter, $str);
        $array = array_map('trim', $array);
        $array = array_keys(array_flip($array));
        $array = array_filter($array);

        return $array;
    }

    /**
     * @param $array
     * @param string $delimiter
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);
        $array = array_keys(array_flip($array));
        $array = array_filter($array);
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param array $array
     * @return array
     */
    public function cleanArray(array $array = array())
    {
        $array = array_map('trim', $array);
        $array = array_filter($array);
        $array = array_keys(array_flip($array));

        return $array;
    }

    /**
     * @param $needle
     * @param array $array
     * @param bool $all
     * @return array
     */
    public function removeArrayByValue($needle, $array = array(), $all = true)
    {
        if (!$all) {
            if (FALSE !== $key = array_search($needle, $array)) unset($array[$key]);
            return $array;
        }
        foreach (array_keys($array, $needle) as $key) {
            unset($array[$key]);
        }
        return $array;
    }

    /**
     * @param array $arr
     * @return string|null
     */
    public function getArrayFirstKey(array $arr)
    {
        $keys = array_keys($arr);
        return empty($keys) ? null : $keys[0];
    }


    /**
     * @param array $arr
     * @param string $oldKey
     * @param string $newKey
     * @return array
     */
    function changeArrayKey(array $arr, $oldKey, $newKey)
    {
        if (!array_key_exists($oldKey, $arr)) {
            return $arr;
        }
        $keyPos = array_search($oldKey, array_keys($arr));
        $arrBefore = array_slice($arr, 0, $keyPos);
        $arrAfter = array_slice($arr, count($arrBefore) + 1);
        $arrRenamed = array($newKey => $arr[$oldKey]);
        return $arrBefore + $arrRenamed + $arrAfter;
    }


    /**
     * @param string $path
     * @param bool $normalize
     * @return mixed|string
     */
    public function preparePath($path = '', $normalize = false)
    {
        if (empty($path)) return '';
        $path = str_replace(array(
            '{base_path]',
            '{core_path}',
            '{assets_path}',
            '{assets_url}',
            '{mgr_path}',
            '{+core_path}',
            '{+assets_path}',
            '{+assets_url}',
        ), array(
            $this->modx->getOption('base_path', null, MODX_BASE_PATH),
            $this->modx->getOption('core_path', null, MODX_CORE_PATH),
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH),
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL),
            $this->modx->getOption('mgr_path', null, MODX_MANAGER_PATH),
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msimportexport/',
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/msimportexport/',
            $this->modx->getOption('assets_url', null, MODX_ASSETS_PATH) . 'components/msimportexport/',
        ), $path);
        return $normalize ? $this->normalizePath($path) : $path;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getWorkingDirectoryByFile($file)
    {
        return pathinfo($file, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getWorkingDirectoryNameByFile($file)
    {
        return $this->basename($this->getWorkingDirectoryByFile($file));
    }

    /**
     * @param string $addition
     * @return bool
     */
    public function hasAddition($addition = '')
    {
        $addition = strtolower($addition);
        return file_exists(MODX_CORE_PATH . 'components/' . $addition . '/model/' . $addition . '/');
    }

    /**
     * @param int $number
     *
     * @return float
     */
    public function formatNumber($number = 0, $ceil = false)
    {
        $number = str_replace(',', '.', $number);
        $number = (float)$number;

        if ($ceil) {
            $number = ceil($number / 10) * 10;
        }

        return round($number, 3);
    }

    /**
     * @param int $ms
     * @return string
     */
    public function formatMilliseconds($ms)
    {
        $seconds = floor($ms / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $milliseconds = $ms % 1000;
        $seconds = $seconds % 60;
        $minutes = $minutes % 60;

        $format = '%02u:%02u:%02u.%04u';
        $time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);
        return rtrim($time, '0');
    }

    /**
     * @param $text
     * @param string $method
     * @return string
     */
    public function formatText($text, $method = 'nl2br')
    {
        if (empty($text)) return '';
        switch ($method) {
            case 'nl2br';
                $text = nl2br($text);
                break;
            case 'paragraph';
                if ($arr = preg_split('/\r\n|\r|\n/', $text)) {
                    $text = '';
                    foreach ($arr as $val) {
                        $val = trim($val);
                        if (empty($val)) continue;
                        $text .= "<p>{$val}</p>";
                    }
                }
                break;
        }
        return $text;
    }

    /**
     * @param string $email
     * @param string $subject
     * @param string $body
     * @return void
     */
    public function sendEmail($email, $subject, $body = '')
    {

        /** @var modPHPMailer $mail */
        $mail = $this->modx->getService('mail', 'mail.modPHPMailer');
        $mail->setHTML(true);

        $mail->address('to', trim($email));
        $mail->set(modMail::MAIL_SUBJECT, trim($subject));
        $mail->set(modMail::MAIL_BODY, $body);
        $mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        if (!$mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo
            );
        }
        $mail->reset();
    }

    /**
     * Get the true client IP. Returns an array of values:
     *
     * * ip - The real, true client IP
     * * suspected - The suspected IP, if not alike to REMOTE_ADDR
     * * network - The client's network IP
     *
     * @access public
     * @return array
     */
    public function getClientIp()
    {
        $ip = '';
        $ipAll = array(); // networks IP
        $ipSus = array(); // suspected IP

        $serverVariables = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_COMING_FROM',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_COMING_FROM',
            'HTTP_CLIENT_IP',
            'HTTP_FROM',
            'HTTP_VIA',
            'REMOTE_ADDR',
        );

        foreach ($serverVariables as $serverVariable) {
            $value = '';
            if (isset($_SERVER[$serverVariable])) {
                $value = $_SERVER[$serverVariable];
            } elseif (getenv($serverVariable)) {
                $value = getenv($serverVariable);
            }

            if (!empty($value)) {
                $tmp = explode(',', $value);
                $ipSus[] = $tmp[0];
                $ipAll = array_merge($ipAll, $tmp);
            }
        }

        $ipSus = array_unique($ipSus);
        $ipAll = array_unique($ipAll);
        $ip = (sizeof($ipSus) > 0) ? $ipSus[0] : $ip;

        return array(
            'ip' => $ip,
            'suspected' => $ipSus,
            'network' => $ipAll,
        );
    }

    /**
     * @param string|array $message
     * @param int $level
     */
    public function log($message, $level = modX::LOG_LEVEL_ERROR)
    {
        if (is_array($message)) {
            $message = print_r($message, 1);
        }
        $curLevel = $this->modx->getLogLevel();
        $this->modx->setLogLevel($level);
        $this->modx->log($level, $message);
        $this->modx->setLogLevel($curLevel);
    }

    /**
     * @param string|array $message
     */
    public function debug($message)
    {

        if (is_array($message)) {
            $message = print_r($message, 1);
        }
        $this->log($message, modX::LOG_LEVEL_DEBUG);
    }

    /**
     * @param int $rid
     * @param int $tvId
     * @param string $val
     * @return bool
     */
    public function updateTv($rid, $tvId, $val)
    {
        $className = 'modTemplateVarResource';
        $table = $this->modx->getTableName($className);
        $val = $this->modx->quote($val);
        $sql = "INSERT INTO {$table} (`contentid`, `tmplvarid`, `value`) VALUES({$rid}, {$tvId}, {$val})  ON DUPLICATE KEY UPDATE  `value` = {$val}";
        if ($this->modx->exec($sql)) {
            return true;
        } else {
            $err = $this->modx->pdo->errorInfo();
            switch ($err[0]) {
                case '01000':
                case '00000':
                    return true;
                    break;
                default:
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[update Tv]  error info: ' . print_r($err, 1) . "\nSQL: " . $sql);
            }
            return false;
        }
    }

    /**
     * @param string $sql
     * @return bool
     */
    public function execSql($sql)
    {
        if (!$this->modx->exec($sql)) {
            $err = $this->modx->pdo->errorInfo();
            switch ($err[0]) {
                case '01000':
                case '00000':
                    break;
                default:
                {
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[execSql] Error info: ' . print_r($err, 1) . "\nSQL: " . $sql);
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * @param string $field
     * @return bool
     */
    public function isFieldArrayType($field)
    {

        if ($type = $this->getProductFieldType($field)) {
            if ($type == 'json' || $type == 'array') {
                return true;
            }
        } else if ($type = $this->getProductOptionFieldType($field)) {
            if ($type == 'combo-multiple' || $type == 'combo-options') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $options
     * @return array
     */
    public function getDeletedResourceIds(array $options = array())
    {
        $result = array();
        $ctx = $this->modx->getOption('ctx', $options, '', true);
        $classKey = $this->modx->getOption('class_key', $options, '', true);
        $q = $this->modx->newQuery('modResource');
        $q->select($this->modx->getSelectColumns('modResource', 'modResource', '', array('id')));
        $q->where(array(
            'deleted' => 1,
        ));

        if (!empty($classKey)) {
            $q->where(array(
                'class_key' => $classKey,
            ));
        }
        if (!empty($ctx)) {
            $q->where(array('context_key' => $ctx));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $result;
    }

    /**
     * @param xPDO $xpdo
     * @param int $sessionWaitTimeout
     * @return bool
     */
    public static function reconnect(xPDO &$xpdo, $sessionWaitTimeout = 0)
    {
        $xpdo->connection->pdo = null;
        if ($xpdo->connect(null, array(xPDO::OPT_CONN_MUTABLE => true))) {
            if ($sessionWaitTimeout) {
                self::setSessionWaitTimeout($xpdo, $sessionWaitTimeout);
            }
            return true;
        }
        return false;
    }

    /**
     * @param xPDO $xpdo
     * @param $time
     */
    public static function setSessionWaitTimeout(xPDO &$xpdo, $time)
    {
        if ($time <= 0) return;
        $xpdo->exec("set session wait_timeout={$time};");
        //$xpdo->exec("set session wait_timeout={$time}; set session interactive_timeout={$time};");
    }

}
