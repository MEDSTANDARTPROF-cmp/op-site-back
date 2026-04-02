<?php
/**
 * @var modX $modx
 * @package msimportexport
 */

require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$msie = $modx->getService('msimportexport', 'Msie');

/* handle request */
$path = $modx->getOption('processorsPath', $msie->config);
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
