<?php
/**
 * ieMsOptionsPrice2 Connector
 * @package iemsoptionsprice2
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('iemsoptionsprice2.core_path',null,$modx->getOption('core_path').'components/iemsoptionsprice2/');
require_once $corePath.'model/iemsoptionsprice2/iemsoptionsprice2.class.php';
$modx->iemsoptionsprice2 = new IeMsOptionsPrice2($modx);

$modx->lexicon->load('iemsoptionsprice2:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->iemsoptionsprice2->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
