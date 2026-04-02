<?php

if (PHP_SAPI !== 'cli') die("Cmd line access only!\n");
define('MODX_API_MODE', true);

require_once dirname(__FILE__, 4) . '/config/config.inc.php';
require_once MODX_BASE_PATH . 'index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var Msie $msie */
$msie = $modx->getService('msimportexport', 'Msie');

/** @var  MsIeWatcher $watcher */
if ($watcher = $msie->getWatcher()) {
    $watcher->run();
}