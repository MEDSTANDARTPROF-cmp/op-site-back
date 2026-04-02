<?php return array (
  'b305df537e695ca193bc017a8d204121' => 
  array (
    'criteria' => 
    array (
      'name' => 'easyredirects',
    ),
    'object' => 
    array (
      'name' => 'easyredirects',
      'path' => '{core_path}components/easyredirects/',
      'assets_path' => '',
    ),
  ),
  '095d09a62f2da8d9387c4eeee4dd9c69' => 
  array (
    'criteria' => 
    array (
      'text' => 'easyredirects',
    ),
    'object' => 
    array (
      'text' => 'easyredirects',
      'parent' => 'components',
      'action' => 'home',
      'description' => 'easyredirects_menu_desc',
      'icon' => '',
      'menuindex' => 0,
      'params' => '',
      'handler' => '',
      'permissions' => '',
      'namespace' => 'easyredirects',
    ),
  ),
  'e3486cc99c1fb513a66fd64578bca9e1' => 
  array (
    'criteria' => 
    array (
      'key' => 'easyredirects_track',
    ),
    'object' => 
    array (
      'key' => 'easyredirects_track',
      'value' => '',
      'xtype' => 'combo-boolean',
      'namespace' => 'easyredirects',
      'area' => 'easyredirects_main',
      'editedon' => NULL,
    ),
  ),
  'c4340aaa68ca9c035949023b7aeb0ea2' => 
  array (
    'criteria' => 
    array (
      'category' => 'easyRedirects',
    ),
    'object' => 
    array (
      'id' => 28,
      'parent' => 0,
      'category' => 'easyRedirects',
      'rank' => 0,
    ),
  ),
  '14638872b1a8d718c7042b5634b17e63' => 
  array (
    'criteria' => 
    array (
      'name' => 'easyRedirects',
    ),
    'object' => 
    array (
      'id' => 19,
      'source' => 1,
      'property_preprocess' => 0,
      'name' => 'easyRedirects',
      'description' => '',
      'editor_type' => 0,
      'category' => 28,
      'cache_type' => 0,
      'plugincode' => '/**
 * @package easyRedirects
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var modResource $resource
 * @var string $mode
 */

/** @var easyRedirects $easyRedirects */
$corePath = $modx->getOption(\'easyredirects_core_path\', $scriptProperties, $modx->getOption(\'core_path\') . \'components/easyredirects/\');
$easyRedirects = $modx->getService(\'easyRedirects\', \'easyRedirects\', $corePath . \'model/\', $scriptProperties);
if (!($easyRedirects instanceof easyRedirects)) {
    return;
}

switch ($modx->event->name) {
    case \'OnPageNotFound\':
        /* handle redirects */
        $search = rawurldecode($_SERVER[\'REQUEST_URI\']);
        $baseUrl = trim($modx->getOption(\'base_url\', null, MODX_BASE_URL));
        if (!empty($baseUrl) && $baseUrl != \'/\' && $baseUrl != \'/\' . $modx->context->get(\'key\') . \'/\') {
            $search = str_replace($baseUrl, \'\', $search);
        }

        $search = ltrim($search, \'/\');
        if (!empty($search)) {
            $quotedSearch = $modx->quote($search);

            /** @var xPDOQuery $criteria */
            $criteria = $modx->newQuery(\'easyRedirect\');
            $criteria->where([
                \'url\' => $search,
                [
                    \'context_key:=\' => $modx->context->get(\'key\'),
                    \'OR:context_key:IS\' => null,
                    \'OR:context_key:=\' => \'\'
                ],
                \'active\' => true
            ]);

            /** @var easyRedirect $redirect */
            $redirect = $modx->getObject(\'easyRedirect\', $criteria);

            if (empty($redirect) || !is_object($redirect)) {
                // Внимание.
                // Вторая строчка в where закомментирована по той причине, что:
                // для страницы catalog/category/product подойдет правило "category".
                // хотя по логике оно бы должно работать только на корневую страницу.
                $criteria2 = $modx->newQuery(\'easyRedirect\');
                $criteria2->where([
                    "("
                    . "`easyRedirect`.`url` = " . $quotedSearch
                    //. " OR " . $quotedSearch . " REGEXP `easyRedirect`.`url`"
                    . " OR " . $quotedSearch . " REGEXP CONCAT(\'^\', `easyRedirect`.`url`, \'$\')"
                    . ")",
                    [
                        \'context_key:=\' => $modx->context->get(\'key\'),
                        \'OR:context_key:IS\' => null,
                        \'OR:context_key:=\' => \'\'
                    ],
                    \'active\' => true
                ]);
                $redirect = $modx->getObject(\'easyRedirect\', $criteria2);
            }

            if (!empty($redirect) && is_object($redirect)) {
                /** @var modContext $context */
                $context = $redirect->getOne(\'Context\');
                if (empty($context) || !($context instanceof modContext)) {
                    $context = $modx->context;
                }

                $target = $redirect->get(\'target\');

                if ($target != $modx->resourceIdentifier && $target != $search) {
                    if (strpos($target, \'$\') !== false) {
                        $url = $redirect->get(\'url\');
                        $target = preg_replace(\'/\' . $url . \'/\', $target, $search);
                    }
                    if (!strpos($target, \'://\')) {
                        $target = rtrim($context->getOption(\'site_url\'), \'/\') . \'/\' . (($target == \'/\') ? \'\' : ltrim($target, \'/\'));
                    }
                    $modx->log(modX::LOG_LEVEL_INFO, \'[easyRedirects] Redirecting request for \' . $search . \' to \' . $target);

                    $redirect->registerTrigger();

                    // http redirect
                    $statusCodes = [
                        \'301\' => \'HTTP/1.1 301 Moved Permanently\',
                        \'302\' => \'HTTP/1.1 302 Found\',
                        \'307\' => \'HTTP/1.1 307 Temporary Redirect\',
                        \'308\' => \'HTTP/1.1 308 Permanent Redirect\',
                    ];

                    $responseCode = $redirect->get(\'response_code\');
                    if(!array_key_exists($responseCode, $statusCodes)) {
                        $responseCode = \'301\';
                    }

                    $options = array(\'responseCode\' => $statusCodes[$responseCode]);
                    // Comment this line for debug
                    $modx->sendRedirect($target, $options);
                }
            }
        }

        break;

    case \'OnDocFormRender\':
        $track = (bool)$modx->getOption(\'easyredirects_track\', null, 0);

        if ($mode == \'upd\' && $track) {
            $_SESSION[\'modx_resource_uri\'] = $resource->get(\'uri\');
        }

        break;

    case \'OnDocFormSave\':
        /* if uri has changed, add to redirects */
        $track = (bool)$modx->getOption(\'easyredirects_track\', null, 0);

        if ($mode == \'upd\' && $track && !empty($_SESSION[\'modx_resource_uri\'])) {
            $contextKey = $resource->get(\'context_key\');
            $newUri = $resource->get(\'uri\');


            $oldUri = $_SESSION[\'modx_resource_uri\'];
            if ($oldUri != $newUri) {
                /* uri changed */
                $redirect = $modx->getObject(\'easyRedirect\', array(
                    \'url\' => $oldUri,
                    \'context_key\' => $contextKey,
                    // \'active\' => true
                ));
                if (empty($redirect)) {
                    /* no record for old uri */
                    $redirect = $modx->newObject(\'easyRedirect\');
                    $redirect->fromArray(array(
                        \'url\' => $oldUri,
                        // TODO: по идее лучше сохранять id ресурса, а не uri
                        \'target\' => $resource->get(\'uri\'),
                        \'context_key\' => $contextKey,
                        \'active\' => true,
                    ));

                    if ($redirect->save() == false) {
                        return $modx->error->failure(\'[easyRedirects] \' . $modx->lexicon(\'easyredirects_redirect_err_save\'));
                    }
                }
            }

            $_SESSION[\'modx_resource_uri\'] = $newUri;
        }

        break;
}',
      'locked' => 0,
      'properties' => NULL,
      'disabled' => 0,
      'moduleguid' => '',
      'static' => 0,
      'static_file' => 'core/components/easyredirects/elements/plugins/easyredirects.php',
      'content' => '/**
 * @package easyRedirects
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var modResource $resource
 * @var string $mode
 */

/** @var easyRedirects $easyRedirects */
$corePath = $modx->getOption(\'easyredirects_core_path\', $scriptProperties, $modx->getOption(\'core_path\') . \'components/easyredirects/\');
$easyRedirects = $modx->getService(\'easyRedirects\', \'easyRedirects\', $corePath . \'model/\', $scriptProperties);
if (!($easyRedirects instanceof easyRedirects)) {
    return;
}

switch ($modx->event->name) {
    case \'OnPageNotFound\':
        /* handle redirects */
        $search = rawurldecode($_SERVER[\'REQUEST_URI\']);
        $baseUrl = trim($modx->getOption(\'base_url\', null, MODX_BASE_URL));
        if (!empty($baseUrl) && $baseUrl != \'/\' && $baseUrl != \'/\' . $modx->context->get(\'key\') . \'/\') {
            $search = str_replace($baseUrl, \'\', $search);
        }

        $search = ltrim($search, \'/\');
        if (!empty($search)) {
            $quotedSearch = $modx->quote($search);

            /** @var xPDOQuery $criteria */
            $criteria = $modx->newQuery(\'easyRedirect\');
            $criteria->where([
                \'url\' => $search,
                [
                    \'context_key:=\' => $modx->context->get(\'key\'),
                    \'OR:context_key:IS\' => null,
                    \'OR:context_key:=\' => \'\'
                ],
                \'active\' => true
            ]);

            /** @var easyRedirect $redirect */
            $redirect = $modx->getObject(\'easyRedirect\', $criteria);

            if (empty($redirect) || !is_object($redirect)) {
                // Внимание.
                // Вторая строчка в where закомментирована по той причине, что:
                // для страницы catalog/category/product подойдет правило "category".
                // хотя по логике оно бы должно работать только на корневую страницу.
                $criteria2 = $modx->newQuery(\'easyRedirect\');
                $criteria2->where([
                    "("
                    . "`easyRedirect`.`url` = " . $quotedSearch
                    //. " OR " . $quotedSearch . " REGEXP `easyRedirect`.`url`"
                    . " OR " . $quotedSearch . " REGEXP CONCAT(\'^\', `easyRedirect`.`url`, \'$\')"
                    . ")",
                    [
                        \'context_key:=\' => $modx->context->get(\'key\'),
                        \'OR:context_key:IS\' => null,
                        \'OR:context_key:=\' => \'\'
                    ],
                    \'active\' => true
                ]);
                $redirect = $modx->getObject(\'easyRedirect\', $criteria2);
            }

            if (!empty($redirect) && is_object($redirect)) {
                /** @var modContext $context */
                $context = $redirect->getOne(\'Context\');
                if (empty($context) || !($context instanceof modContext)) {
                    $context = $modx->context;
                }

                $target = $redirect->get(\'target\');

                if ($target != $modx->resourceIdentifier && $target != $search) {
                    if (strpos($target, \'$\') !== false) {
                        $url = $redirect->get(\'url\');
                        $target = preg_replace(\'/\' . $url . \'/\', $target, $search);
                    }
                    if (!strpos($target, \'://\')) {
                        $target = rtrim($context->getOption(\'site_url\'), \'/\') . \'/\' . (($target == \'/\') ? \'\' : ltrim($target, \'/\'));
                    }
                    $modx->log(modX::LOG_LEVEL_INFO, \'[easyRedirects] Redirecting request for \' . $search . \' to \' . $target);

                    $redirect->registerTrigger();

                    // http redirect
                    $statusCodes = [
                        \'301\' => \'HTTP/1.1 301 Moved Permanently\',
                        \'302\' => \'HTTP/1.1 302 Found\',
                        \'307\' => \'HTTP/1.1 307 Temporary Redirect\',
                        \'308\' => \'HTTP/1.1 308 Permanent Redirect\',
                    ];

                    $responseCode = $redirect->get(\'response_code\');
                    if(!array_key_exists($responseCode, $statusCodes)) {
                        $responseCode = \'301\';
                    }

                    $options = array(\'responseCode\' => $statusCodes[$responseCode]);
                    // Comment this line for debug
                    $modx->sendRedirect($target, $options);
                }
            }
        }

        break;

    case \'OnDocFormRender\':
        $track = (bool)$modx->getOption(\'easyredirects_track\', null, 0);

        if ($mode == \'upd\' && $track) {
            $_SESSION[\'modx_resource_uri\'] = $resource->get(\'uri\');
        }

        break;

    case \'OnDocFormSave\':
        /* if uri has changed, add to redirects */
        $track = (bool)$modx->getOption(\'easyredirects_track\', null, 0);

        if ($mode == \'upd\' && $track && !empty($_SESSION[\'modx_resource_uri\'])) {
            $contextKey = $resource->get(\'context_key\');
            $newUri = $resource->get(\'uri\');


            $oldUri = $_SESSION[\'modx_resource_uri\'];
            if ($oldUri != $newUri) {
                /* uri changed */
                $redirect = $modx->getObject(\'easyRedirect\', array(
                    \'url\' => $oldUri,
                    \'context_key\' => $contextKey,
                    // \'active\' => true
                ));
                if (empty($redirect)) {
                    /* no record for old uri */
                    $redirect = $modx->newObject(\'easyRedirect\');
                    $redirect->fromArray(array(
                        \'url\' => $oldUri,
                        // TODO: по идее лучше сохранять id ресурса, а не uri
                        \'target\' => $resource->get(\'uri\'),
                        \'context_key\' => $contextKey,
                        \'active\' => true,
                    ));

                    if ($redirect->save() == false) {
                        return $modx->error->failure(\'[easyRedirects] \' . $modx->lexicon(\'easyredirects_redirect_err_save\'));
                    }
                }
            }

            $_SESSION[\'modx_resource_uri\'] = $newUri;
        }

        break;
}',
    ),
  ),
  'e1a7f9318952cfaaf63c23c8991e8e05' => 
  array (
    'criteria' => 
    array (
      'pluginid' => 19,
      'event' => 'OnPageNotFound',
    ),
    'object' => 
    array (
      'pluginid' => 19,
      'event' => 'OnPageNotFound',
      'priority' => 0,
      'propertyset' => 0,
    ),
  ),
  'b6dee6f9e44f319dd77754b40bfd7fad' => 
  array (
    'criteria' => 
    array (
      'pluginid' => 19,
      'event' => 'OnDocFormRender',
    ),
    'object' => 
    array (
      'pluginid' => 19,
      'event' => 'OnDocFormRender',
      'priority' => 0,
      'propertyset' => 0,
    ),
  ),
  'b441a62d29b35e7cfad3d96a2f6c0e88' => 
  array (
    'criteria' => 
    array (
      'pluginid' => 19,
      'event' => 'OnDocFormSave',
    ),
    'object' => 
    array (
      'pluginid' => 19,
      'event' => 'OnDocFormSave',
      'priority' => 0,
      'propertyset' => 0,
    ),
  ),
);