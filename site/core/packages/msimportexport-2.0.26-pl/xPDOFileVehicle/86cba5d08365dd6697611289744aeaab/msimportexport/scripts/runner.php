<?php
/**
 * Task runner script
 */


if (PHP_SAPI !== 'cli') die("Cmd line access only!\n");
if (
    count($argv) < 3 ||
    !is_numeric($argv[1]) ||
    !is_string($argv[2])
) {
    die("Invalid call!\n");
}

array_shift($argv);

define('MODX_API_MODE', true);

require_once dirname(__FILE__, 4) . '/config/config.inc.php';
require_once MODX_BASE_PATH . 'index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

/** @var Msie $msie */
$msie = $modx->getService('msimportexport', 'Msie');

/** @var MsieTask $task */
$task = $modx->getObject('MsieTask', $argv[0]);

if (!$task) die("Invalid task ID!\n");
if (!method_exists($task, $argv[1])) die("Method: {$argv[1]} not found in class MsieTask!\n");

call_user_func(array($task, $argv[1]));

