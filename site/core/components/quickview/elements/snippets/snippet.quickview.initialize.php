<?php

/** @var array $scriptProperties */
$corePath = $modx->getOption('quickview_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/quickview/');
/** @var QuickView $QuickView */
$QuickView = $modx->getService('quickview', 'QuickView', $corePath . 'model/quickview/',
    array('core_path' => $corePath));
if (!$QuickView OR !($QuickView instanceof QuickView)) {
    return 'Could not load QuickView class!';
}

$services = $modx->getOption('services', $scriptProperties);
$services = $QuickView->explodeAndClean($services);
foreach ($services as $service) {

    $class = strtolower($service);
    $fqn = $modx->getOption("{$class}_class", null, "{$class}.{$service}", true);
    $path = $modx->getOption("{$class}_core_path", null,
        $modx->getOption("core_path", null, MODX_CORE_PATH) . "components/{$class}/");
    if ($instance = $modx->getService($fqn, '', $path . 'model/', array('core_path' => $path))) {
        $instance->initialize($modx->context->key);
    }

    /** @var modSnippet $snippet */
    if ($snippet = $modx->getObject('modSnippet', array('name' => "{$service}.initialize"))) {
        $snippet->process();
    }

}

$QuickView->initialize($modx->context->key, $scriptProperties);