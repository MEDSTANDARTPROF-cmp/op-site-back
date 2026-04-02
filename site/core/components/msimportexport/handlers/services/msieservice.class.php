<?php

abstract class MsIeService
{

    /** @var modX $modx */
    public $modx;
    /** @var MsIeTools $tools */
    public $tools;
    /** @var array $config */
    public $config = array();
    /** @var MsIeWorker $worker */
    protected $worker;
    /** @var int $rank */
    protected $rank = 0;

    /**
     * MsieService constructor.
     * @param MsIeTools $tools
     * @param array $config
     */
    public function __construct(MsIeTools &$tools, array $config = array())
    {

        $this->tools = &$tools;
        $this->modx = &$tools->modx;
        $this->config = array_merge($this->config, $config);
        $this->initialize();
    }


    public function initialize()
    {

    }

    /**
     * @return string
     */
    final public function getName()
    {
        return static::class;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::class;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getHomeLink()
    {
        return '';
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return array();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return '';
    }

    /**
     * @param bool $checking
     * @return array
     */
    public function getFields($checking = false)
    {
        if ($checking) {
            return $this->getCheckingFields();
        } else {
            return $this->getCustomFields();
        }
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getCheckingFields()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getExcludeFields()
    {
        return array();
    }

    /**
     * @return string
     */
    public function getParentClassKey()
    {
        return 'modResource';
    }

    /**
     * @return array
     */
    public static function getAllowedFileExtensions()
    {
        return array(
            'csv',
            'tsv',
            'xlsx',
            'ods',
        );
    }

    /**
     * @param $className
     * @param array $properties
     * @return MsIeWorker|null
     */
    protected function loadWorker($className, array $properties = array())
    {
        if (!is_object($this->worker) || !($this->worker instanceof MsIeWorker)) {
            $workerClass = $this->modx->loadClass($className, $this->config['workersPath'], true, true);
            if ($workerClass) {
                $this->worker = new $workerClass($this, $properties);
            }
        }
        return $this->worker;
    }

    /**
     * @param array $properties
     * @return MsIeWorker|null
     */
    abstract public function getWorker(array $properties = array());

}