<?php
/**
 * ieGallery Connector
 * @package iegallery
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('iegallery.core_path',null,$modx->getOption('core_path').'components/iegallery/');
require_once $corePath.'model/iegallery/iegallery.class.php';
$modx->iegallery = new IeGallery($modx);

$modx->lexicon->load('iegallery:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->iegallery->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
