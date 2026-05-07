<?php
define('MODX_API_MODE', true);
require_once dirname(__DIR__).'/site/index.php';

$json = json_decode(file_get_contents(__DIR__.'/_marketing.json'), true);
$d   = $json['resource'];
$tvs = $json['tvs'];

// 1. Make sure container 5454 is published
$cnt = $modx->getObject('modResource', 5454);
if (!$cnt) { echo "Container 5454 NOT FOUND\n"; exit(1); }
if (!$cnt->get('published')) {
    $cnt->set('published', 1);
    $cnt->set('publishedon', time());
    $cnt->set('publishedby', 1);
    $cnt->save();
    echo "Container 5454 published.\n";
} else {
    echo "Container 5454 already published.\n";
}

// 2. Check if a resource with parent=5454 alias=marketing already exists
$exist = $modx->getObject('modResource', ['parent'=>5454, 'alias'=>'marketing', 'deleted'=>0]);
if ($exist) {
    echo "Resource at /kursyi/marketing.html already exists locally (id=".$exist->get('id')."). Aborting to avoid double-creation.\n";
    exit(0);
}

// 3. Make sure msProduct class loaded (miniShop2)
$msPath = MODX_CORE_PATH.'components/minishop2/model/minishop2/minishop2.class.php';
if (file_exists($msPath)) {
    $modx->getService('miniShop2', 'miniShop2', MODX_CORE_PATH.'components/minishop2/model/minishop2/');
}

// 4. Create new resource
$r = $modx->newObject($d['class_key'] ?: 'modResource');

$skip = ['id','uri','uri_override']; // let MODX rebuild URI
foreach ($d as $col => $val) {
    if (in_array($col, $skip)) continue;
    if ($val === null) continue;
    $r->set($col, $val);
}
// Force a fresh editedon
$r->set('editedon', time());
$r->set('editedby', 1);

if (!$r->save()) {
    echo "save() failed\n";
    print_r($modx->errorInfo());
    exit(1);
}

$newId = $r->get('id');
echo "Created resource id=$newId, class=".$r->get('class_key').", parent=".$r->get('parent').", alias=".$r->get('alias')."\n";

// 5. Set TV values
foreach ($tvs as $tv) {
    $r->setTVValue($tv['tmplvarid'], $tv['value']);
    echo "  set TV id=".$tv['tmplvarid']."\n";
}

// 6. Refresh URI map and cache
$modx->cacheManager->refresh([
    'db' => [],
    'auto_publish' => ['contexts' => ['web']],
    'context_settings' => ['contexts' => ['web']],
    'resource' => ['contexts' => ['web']],
]);
echo "Cache refreshed.\n";

// 7. Verify URI
$check = $modx->getObject('modResource', $newId);
echo "Final uri: ".$check->get('uri')."\n";
echo "Published: ".$check->get('published').", deleted: ".$check->get('deleted')."\n";
