<?php
/**
 * Экспорт всех затронутых сущностей B24-cleanup в папку _b24_deploy/
 * + готовит replay-скрипт для прода.
 *
 * Запуск (на ЛОКАЛИ):  php scripts/_b24_deploy_to_prod.php export
 * После — этот скрипт скопировать на прод и запустить:  php8.3 scripts/_b24_deploy_to_prod.php apply
 */
define('MODX_API_MODE', true);
$idx = dirname(__DIR__).'/site/index.php';
if (!file_exists($idx)) $idx = dirname(__DIR__).'/index.php';
require_once $idx;

$mode = $argv[1] ?? 'export';
$root = __DIR__.'/_b24_deploy';

if ($mode === 'export') {
    @mkdir($root.'/templates', 0777, true);
    @mkdir($root.'/chunks', 0777, true);
    @mkdir($root.'/resources_content', 0777, true);

    // 7 шаблонов
    foreach ([3, 5, 8, 14, 15, 21, 27] as $tid) {
        $t = $modx->getObject('modTemplate', $tid);
        if ($t) file_put_contents($root.'/templates/template_'.$tid.'.html', $t->get('content'));
    }
    echo "templates: 7\n";

    // Чанки: 19 changed by cleanup + ContentBanner replaced + contRek com2->com fix
    $chunks = [
        'Navbar','Footer','contAdres','msProduct.content.prod','boxManager','boxComerch',
        'catalogFooter','SaleCard.tpl','boxFormNews','tpl.msProducts.row.Obr.Modal',
        'catalogCard.tpl.modal','ContentBanner','catalogHeaderDPO','catalogFooterDPO',
        'ContentBannerSout','msProduct.content.PSK','boxCTABanner','boxGuaranteePSK',
        'msProduct.content.PSK_partner','contRek'
    ];
    $cnt = 0;
    foreach ($chunks as $name) {
        $c = $modx->getObject('modChunk', ['name'=>$name]);
        if ($c) {
            file_put_contents($root.'/chunks/'.str_replace('.','_',$name).'.html', $c->get('snippet'));
            $cnt++;
        }
    }
    echo "chunks: $cnt\n";

    // Ресурсы — точечный фильтр: те у кого в content появился #MdGlNew (наша замена)
    // Plus resource 29 (partneram, Тимур-кнопка)
    $q = $modx->newQuery('modResource', ['deleted'=>0]);
    $q->where(['content:LIKE'=>'%#MdGlNew%']);
    $rows = $modx->getCollection('modResource', $q);
    $resCnt = 0;
    $ids = [];
    foreach ($rows as $r) {
        $rid = $r->get('id');
        $ids[] = $rid;
        file_put_contents($root.'/resources_content/res_'.$rid.'.html', (string)$r->get('content'));
        $resCnt++;
    }
    if (!in_array(29, $ids)) {
        $r = $modx->getObject('modResource', 29);
        if ($r) {
            file_put_contents($root.'/resources_content/res_29.html', (string)$r->get('content'));
            $resCnt++;
        }
    }
    echo "resources: $resCnt\n";

    // Записать manifest
    file_put_contents($root.'/manifest.txt',
        "Exported: ".date('Y-m-d H:i:s')."\n".
        "Templates: ".implode(',', [3,5,8,14,15,21,27])."\n".
        "Chunks: ".implode(',', $chunks)."\n".
        "Resources: $resCnt (from CSV resources_with_b24.csv + id=29)\n".
        "Additional: mark resource id=4714 as deleted=1\n"
    );
    echo "manifest saved.\n";
    echo "Bundle ready in: $root\n";
}
elseif ($mode === 'apply') {
    // Replay all changes on this instance (intended for prod)
    $ok = 0; $miss = 0;

    echo "[1] Updating templates...\n";
    foreach (glob($root.'/templates/template_*.html') as $f) {
        preg_match('/template_(\d+)\.html$/', $f, $m);
        $tid = (int)$m[1];
        $t = $modx->getObject('modTemplate', $tid);
        if (!$t) { echo "  template $tid NOT FOUND\n"; $miss++; continue; }
        $body = file_get_contents($f);
        $t->set('content', $body);
        $t->save();
        echo "  template $tid: ".strlen($body)." chars\n";
        $ok++;
    }

    echo "\n[2] Updating chunks...\n";
    foreach (glob($root.'/chunks/*.html') as $f) {
        $name = basename($f, '.html');
        // Reverse '_' to '.'
        $candidates = [$name, str_replace('_', '.', $name)];
        // For 'msProduct_content_prod' → 'msProduct.content.prod'
        if (preg_match('/^([^_]+)_content_(.+)$/', $name, $m2)) {
            $candidates[] = $m2[1].'.content.'.str_replace('_','.',$m2[2]);
        }
        $c = null;
        foreach (array_unique($candidates) as $cand) {
            $c = $modx->getObject('modChunk', ['name'=>$cand]);
            if ($c) break;
        }
        if (!$c) { echo "  chunk '$name' NOT FOUND (candidates: ".implode(', ', array_unique($candidates)).")\n"; $miss++; continue; }
        $body = file_get_contents($f);
        $c->set('snippet', $body);
        $c->save();
        echo "  chunk '".$c->get('name')."': ".strlen($body)." chars\n";
        $ok++;
    }

    echo "\n[3] Updating resources content...\n";
    foreach (glob($root.'/resources_content/res_*.html') as $f) {
        preg_match('/res_(\d+)\.html$/', $f, $m);
        $rid = (int)$m[1];
        $r = $modx->getObject('modResource', $rid);
        if (!$r) { echo "  resource $rid NOT FOUND\n"; $miss++; continue; }
        $body = file_get_contents($f);
        $r->set('content', $body);
        $r->save();
        $ok++;
    }
    echo "  updated: ".(count(glob($root.'/resources_content/res_*.html')) - $miss)." resources\n";

    echo "\n[4] Marking resource 4714 (/blog/udalit.html) as deleted...\n";
    $r = $modx->getObject('modResource', 4714);
    if ($r && $r->get('uri') === 'blog/udalit.html') {
        $r->set('deleted', 1);
        $r->set('deletedon', time());
        $r->set('deletedby', 1);
        $r->save();
        echo "  marked deleted=1\n";
    } else {
        echo "  resource 4714 not found or different URI — skip\n";
    }

    echo "\n[5] Refreshing cache...\n";
    $modx->cacheManager->refresh([
        'db' => [],
        'auto_publish' => ['contexts' => ['web']],
        'context_settings' => ['contexts' => ['web']],
        'resource' => ['contexts' => ['web']],
    ]);
    echo "DONE. Total: $ok updates, $miss missing.\n";
}
else {
    echo "Usage: php scripts/_b24_deploy_to_prod.php [export|apply]\n";
}
