<?php
/**
 * ieMsSalePrice Connector
 * @package iemssaleprice
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('iemssaleprice.core_path',null,$modx->getOption('core_path').'components/iemssaleprice/');
require_once $corePath.'model/iemssaleprice/iemssaleprice.class.php';
$modx->iemssaleprice = new IeMsSalePrice($modx);

$modx->lexicon->load('iemssaleprice:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->iemssaleprice->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
