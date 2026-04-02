<?php
/**
 * ieMsProductRemains Connector
 * @package iemsproductremains
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('iemsproductremains.core_path',null,$modx->getOption('core_path').'components/iemsproductremains/');
require_once $corePath.'model/iemsproductremains/iemsproductremains.class.php';
$modx->iemsproductremains = new IeMsProductRemains($modx);

$modx->lexicon->load('iemsproductremains:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->iemsproductremains->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
