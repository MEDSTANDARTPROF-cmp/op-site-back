<?php
/**
 * ieMs2 Connector
 * @package iems2
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('iems2.core_path',null,$modx->getOption('core_path').'components/iems2/');
require_once $corePath.'model/iems2/iems2.class.php';
$modx->iems2 = new IeMs2($modx);

$modx->lexicon->load('iems2:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->iems2->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
