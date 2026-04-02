<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

switch (true) {
    case empty($_REQUEST['action']):
    case empty($_SERVER['HTTP_X_REQUESTED_WITH']):
    case $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest':
        die('Access denied');
}

define('MODX_API_MODE', true);

$productionIndex = dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$developmentIndex = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
if (file_exists($productionIndex)) {
    /** @noinspection PhpIncludeInspection */
    require_once $productionIndex;
} else {
    /** @noinspection PhpIncludeInspection */
    require_once $developmentIndex;
}
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');


//header("Access-Control-Allow-Origin: http://location.vgrish.ru");
//header("Access-Control-Allow-Credentials: true");
//header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//header('Access-Control-Max-Age: 1000');
//header("Access-Control-Allow-Headers: X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version");


$corePath = $modx->getOption('quickview_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/quickview/');

/** @var QuickView $QuickView */
$QuickView = $modx->getService('quickview', 'QuickView', $corePath . 'model/quickview/',
    array('core_path' => $corePath));
$QuickView->initialize($ctx);
$response = $QuickView->runProcessor($_REQUEST['action'], $_REQUEST);
if (is_array($response)) {
    $response = json_encode($response, true);
}

@session_write_close();
echo $response;