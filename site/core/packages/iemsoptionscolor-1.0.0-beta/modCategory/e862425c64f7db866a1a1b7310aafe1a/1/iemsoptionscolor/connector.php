<?php
/**
 * ieMsOptionsColor Connector
 * @package iemsoptionscolor
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('iemsoptionscolor.core_path',null,$modx->getOption('core_path').'components/iemsoptionscolor/');
require_once $corePath.'model/iemsoptionscolor/iemsoptionscolor.class.php';
$modx->iemsoptionscolor = new IeMsOptionsColor($modx);

$modx->lexicon->load('iemsoptionscolor:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->iemsoptionscolor->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
