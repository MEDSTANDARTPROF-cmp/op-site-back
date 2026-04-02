<?php
/**
 * ieYandexMarket Connector
 * @package ieyandexmarket
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('ieyandexmarket.core_path',null,$modx->getOption('core_path').'components/ieyandexmarket/');
require_once $corePath.'model/ieyandexmarket/ieyandexmarket.class.php';
$modx->ieyandexmarket = new IeYandexMarket($modx);

$modx->lexicon->load('ieyandexmarket:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->ieyandexmarket->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
