<?php

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');
/**
 * MODx IeMs2 Class
 *
 * @package msimportexport
 */
class IeMs2
{
    const version = '1.0.0';
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = array();
    /** @var string */
    public $namespace = 'iems2';
    /** @var IeMs2Tools $tools */
    protected $tools;

    /**
     * Creates an instance of the Msie class.
     *
     * @param modX &$modx A reference to the modX instance.
     * @param array $config An array of configuration parameters.
     * @return IeMs2
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('iems2.core_path', $config, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/iems2/');
        $assetsUrl = $this->modx->getOption('iems2.assets_url', $config, $this->modx->getOption('assets_url') . 'components/iems2/');
        $assetsPath = $this->modx->getOption('iems2.assets_path', $config, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/iems2/');

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
            'handlersPath' => $corePath . 'handlers/',
            'servicesPath' => $corePath . 'handlers/services/',
            'workersPath' => $corePath . 'handlers/workers/',
            'readersPath' => $corePath . 'handlers/readers/',
            'writersPath' => $corePath . 'handlers/writers/',
            'toolsHandler' => $this->modx->getOption('iems2_tools_handler_class', null, 'IeMs2Tools'),
        ), $config);

        $this->modx->lexicon->load('iems2:default');
        $this->modx->addPackage('iems2', $this->config['modelPath']);

        // $this->checkStat();
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
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
        $processorsPath = !empty($this->config['processorsPath'])
            ? $this->config['processorsPath']
            : MODX_CORE_PATH . 'components/iems2/processors/';

        return $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath,
        ));
    }

    /**
     * @param array $config
     * @return IeMs2Tools
     */
    public function getTools(array $config = array())
    {

        if (!is_object($this->tools) || !($this->tools instanceof MsIeTools)) {
            $msie = $this->modx->getService('msimportexport', 'Msie');
            $toolsClass = $this->modx->loadClass('tools.' . $this->config['toolsHandler'], $this->config['handlersPath'], true, true);
            if ($toolsClass) {
                $config = array_merge($this->config, $config);
                $this->tools = new $toolsClass($msie, $config);
            }
        }
        return $this->tools;
    }

    protected function checkStat()
{
    $key = strtolower(__CLASS__);
    /** @var modDbRegister $registry */
    $registry = $this->modx->getService('registry', 'registry.modRegistry')
        ->getRegister('user', 'registry.modDbRegister');
    $registry->connect();
    $registry->subscribe('/modstore/' . md5($key));
    if ($res = $registry->read(array('poll_limit' => 1, 'remove_read' => false))) {
        return;
    }
    $c = $this->modx->newQuery('transport.modTransportProvider', array('service_url:LIKE' => '%modstore%'));
    $c->select('username,api_key');
    /** @var modRest $rest */
    $rest = $this->modx->getService('modRest', 'rest.modRest', '', array(
        'baseUrl' => 'https://modstore.pro/extras',
        'suppressSuffix' => true,
        'timeout' => 1,
        'connectTimeout' => 1,
    ));
    if ($rest) {
        $level = $this->modx->getLogLevel();
        $this->modx->setLogLevel(modX::LOG_LEVEL_FATAL);
        $rest->post('stat', array(
            'package' => $key,
            'version' => $this::version,
            'keys' => $c->prepare() && $c->stmt->execute()
                ? $c->stmt->fetchAll(PDO::FETCH_ASSOC)
                : array(),
            'uuid' => $this->modx->uuid,
            'database' => $this->modx->config['dbtype'],
            'revolution_version' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
            'supports' => $this->modx->version['code_name'] . '-' . $this->modx->version['full_version'],
            'http_host' => $this->modx->getOption('http_host'),
            'php_version' => XPDO_PHP_VERSION,
            'language' => $this->modx->getOption('manager_language'),
        ));
        $this->modx->setLogLevel($level);
    }
    $registry->subscribe('/modstore/');
    $registry->send('/modstore/', array(md5($key) => true), array('ttl' => 3600 * 24));
}

}