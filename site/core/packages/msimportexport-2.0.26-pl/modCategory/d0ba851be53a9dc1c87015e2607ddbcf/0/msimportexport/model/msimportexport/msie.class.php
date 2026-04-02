<?php
require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

/**
 * MODx Msie Class
 *
 * @package msimportexport
 */
class Msie
{

    const MODE_IMPORT = 'import';
    const MODE_EXPORT = 'export';

    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();
    /** @var string */
    public $version = '2.0.26-pl';
    /** @var string */
    public $namespace = 'msimportexport';
    public $sessionDownloadKey = 'msie_download';
    /** @var MsIeTools $tools */
    protected $tools;
    /** @var MsIeTaskManager $taskManager */
    protected $taskManager;
    /** @var array $services */
    protected $services = array();

    /**
     * Creates an instance of the Msie class.
     *
     * @param modX &$modx A reference to the modX instance.
     * @param array $config An array of configuration parameters.
     * @return Msie
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('msimportexport.core_path', $config, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msimportexport/');
        $assetsUrl = $this->modx->getOption('msimportexport.assets_url', $config, $this->modx->getOption('assets_url') . 'components/msimportexport/');
        $assetsPath = $this->modx->getOption('msimportexport.assets_path', $config, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/msimportexport/');

        $this->config = array_merge(array(
            'chunksPath' => $corePath . 'elements/chunks/',
            'controllersPath' => $corePath . 'controllers/',
            'corePath' => $corePath,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connector_url' => $assetsUrl . 'connector.php',
            'watcherScriptPath' => $corePath . 'scripts/watcher.php',
            'dataPath' => $corePath . 'data/',
            'handlersPath' => $corePath . 'handlers/',
            'readersPath' => $corePath . 'handlers/readers/',
            'writersPath' => $corePath . 'handlers/writers/',
            'servicesPath' => $corePath . 'handlers/services/',
            'workersPath' => $corePath . 'handlers/workers/',
            'toolsHandler' => $this->modx->getOption('msimportexport_tools_handler_class', null, 'MsIeTools'),
            'watcherHandler' => $this->modx->getOption('msimportexport_watcher_handler_class', null, 'MsIeWatcher'),
            'taskManagerHandler' => $this->modx->getOption('msimportexport_task_manager_handler_class', null, 'MsIeTaskManager'),
            'taskRunner' => $corePath . 'scripts/runner.php',
            'doUrl' => $this->getSiteUrl() . $assetsUrl . 'do.php',
            'exportUrl' => $this->getSiteUrl() . $assetsUrl . 'export.php',


        ), $config);

        $this->modx->lexicon->load('msimportexport:default');
        $this->modx->addPackage('msimportexport', $this->config['modelPath']);
    }

    /**
     * @return string
     */
    public function getUrlScheme()
    {
        $protocol = $this->modx->getOption('server_protocol');
        return $protocol == 'http' ? 'http://' : 'https://';
    }

    /**
     * @param bool $slash
     * @return string
     */
    public function getSiteUrl($slash = false)
    {
        $url = $this->getUrlScheme() . MODX_HTTP_HOST;
        if ($slash) $url .= '/';

        return $url;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getDoUrl()
    {
        return $this->config['doUrl'];
    }

    /**
     * Shorthand for the call of processor
     *
     * @access public
     *
     * @param string $action Path to processor
     * @param array $data Data to be transmitted to the processor
     *
     * @return mixed The result of the processor
     */
    public function runProcessor($action = '', $data = array())
    {
        if (empty($action)) {
            return false;
        }
        $this->modx->error->reset();
        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $this->config['processorsPath'],
        ));
    }

    /**
     * @param $mode
     * @return MsIeService[]
     * @throws Exception
     */
    public function getServices($mode = Msie::MODE_IMPORT)
    {
        $mode = trim($mode);

        if (isset($this->services[$mode]) && !empty($this->services[$mode])) {
            return $this->services[$mode];
        }

        $this->services[$mode] = array();

        if (!$this->modx->loadClass('MsIeService', $this->config['servicesPath'], true, true)) {
            throw new Exception('Could not load service class MsIeService at ' . $this->config['servicesPath']);
        }

        $response = $this->modx->invokeEvent('msieOnLoadServices', array(
                'msie' => $this,
                'mode' => $mode,
            )
        );

        if (is_array($response)) {
            foreach ($response as $items) {
                if (!is_array($items)) continue;
                foreach ($items as $key => $service) {
                    if ($service instanceof MsIeService) {
                        $this->services[$mode][$service->getName()] = $service;
                    }
                }
            }
        }

        if ($this->modx->event) {
            $this->modx->event->_output = null;
        }

        uasort($this->services[$mode], function ($service1, $service2) {
            if ($service1->getRank() == $service2->getRank()) {
                return 0;
            }
            return $service1->getRank() < $service2->getRank() ? -1 : 1;
        });

        return $this->services[$mode];
    }

    /**
     * @param string $mode
     * @param string $name
     * @return MsieService|null
     * @throws Exception
     */
    public function getService($mode, $name)
    {
        $mode = trim($mode);
        $name = trim($name);
        $services = $this->getServices($mode);
        return isset($services[$name]) ? $services[$name] : null;
    }

    /**
     * @param array $config
     * @return MsIeTools
     */
    public function getTools(array $config = array())
    {
        if (!is_object($this->tools) || !($this->tools instanceof MsIeTools)) {
            $toolsClass = $this->modx->loadClass('tools.' . $this->config['toolsHandler'], $this->config['handlersPath'], true, true);
            if ($toolsClass) {
                $config = array_merge($this->config, $config);
                $this->tools = new $toolsClass($this, $config);
            }
        }
        return $this->tools;
    }

    /**
     * @return MsIeTaskManager
     */
    public function getTaskManager()
    {
        if (!is_object($this->taskManager) || !($this->taskManager instanceof MsIeTaskManager)) {
            $managerClass = $this->modx->loadClass('managers.' . $this->config['taskManagerHandler'], $this->config['handlersPath'], true, true);
            if ($managerClass) {
                $this->taskManager = new $managerClass($this);
            }
        }
        return $this->taskManager;
    }

    /**
     * @return MsIeWatcher
     */
    public function getWatcher()
    {
        $watcher = null;
        $watcherClass = $this->modx->loadClass('watchers.' . $this->config['watcherHandler'], $this->config['handlersPath'], true, true);
        if ($watcherClass) {
            $watcher = new $watcherClass($this);
        }
        return $watcher;
    }

    /**
     * @param array $config
     * @return MsIeDownloader|null
     */
    public function getDownloader(array $config = array())
    {
        $downloader = null;
        $class = $this->modx->loadClass('do.MsIeDownloader', $this->config['handlersPath'], true, true);
        if ($class) {
            $downloader = new $class($this, $config);
        }
        return $downloader;
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

}