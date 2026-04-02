<?php
if (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json') {
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    if ($data) {
        switch (strtolower($_SERVER['REQUEST_METHOD'])) {
            case 'get':
                $_GET = $data;
                break;
            case 'post':
                $_POST = $data;
                break;
        }
    }
}

/*if (empty($_POST['action'])) {
    @session_write_close();
    die('Access denied');
}*/


/**
 * @var Msie $msie
 * @var MsieTask $task
 * @var MsiePreset $preset
 */

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$msie = $modx->getService('msimportexport', 'Msie');
if (!$downloader = $msie->getDownloader()) exit();
$downloader->execute();