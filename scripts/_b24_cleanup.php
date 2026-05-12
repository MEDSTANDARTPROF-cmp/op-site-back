<?php
/**
 * B24 form cleanup — заменяет все упоминания старой B24-формы на нашу ObrForm.
 *
 * Действия:
 *   1. В content шаблонов и чанков: удалить inline-script `<script data-b24-form="inline/330/rmpexx">...</script>`
 *      и пустые `<div id="MdGl"...></div>` контейнеры.
 *   2. data-bs-target="#MdGl" → data-bs-target="#MdGlNew"
 *   3. data-marker="X"        → data-form-source="X"
 *
 * Запуск: php scripts/_b24_cleanup.php --dry  | --apply
 */
define('MODX_API_MODE', true);
$idx = dirname(__DIR__).'/site/index.php';
if (!file_exists($idx)) $idx = dirname(__DIR__).'/index.php';
require_once $idx;

$DRY = in_array('--dry', $argv);
$APPLY = in_array('--apply', $argv);
if (!$DRY && !$APPLY) { echo "Usage: php scripts/_b24_cleanup.php --dry | --apply\n"; exit(1); }

/**
 * Применяет 3 трансформации к строке: удаляет inline B24, заменяет MdGl → MdGlNew + marker → source.
 * Возвращает изменённую строку.
 */
function transform(string $body): string {
    // 1. Удалить inline B24-script ЛЮБОЙ формы (loader_330/312/332/etc).
    //    Сначала ловим div-обёртку (container > bg-white > script), потом одиночный script-тег.
    $patterns = [
        // div-обёртка с inline-формой (common pattern: container > bg-white > script)
        '/<div class="container[^"]*"[^>]*>\s*<div class="bg-white[^"]*"[^>]*>\s*<script\s+data-b24-form="inline\/\d+\/[^"]+"[^>]*>.*?<\/script>\s*<\/div>\s*<\/div>/s',
        // обёртка в modal-body
        '/<script\s+data-b24-form="inline\/\d+\/[^"]+"[^>]*>.*?<\/script>/s',
    ];
    foreach ($patterns as $p) {
        $body = preg_replace($p, '', $body);
    }

    // 2. Заменить таргет модалки (старая #MdGl → наша #MdGlNew)
    $body = str_replace('data-bs-target="#MdGl"', 'data-bs-target="#MdGlNew"', $body);
    $body = str_replace("data-bs-target='#MdGl'", "data-bs-target='#MdGlNew'", $body);

    // 2b. Кнопка «Оставить отзыв» (открывает modal id=Otz) — удаляем целиком вместе с её <a>...</a>.
    //     Позже сделаем ссылку на Яндекс.Карты вручную.
    $body = preg_replace('/<a[^>]*data-bs-target=["\']#Otz["\'][^>]*>.*?<\/a>/s', '', $body);
    //     Также удаляем сам modal id="Otz" (он теперь без вызова — пустой)
    $body = preg_replace('/<div\s+class="modal[^"]*"\s+id=["\']Otz["\'][^>]*>.*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s', '', $body);

    // 3. data-marker → data-form-source. Старая модалка не существует, маркер потерял смысл.
    $body = preg_replace('/\bdata-marker=/', 'data-form-source=', $body);

    return $body;
}

$totals = ['tpl'=>0, 'chunk'=>0, 'res'=>0];
$diffs = ['tpl'=>[], 'chunk'=>[], 'res'=>[]];

// === 0. Особая обработка: чанк ContentBanner — заменить B24-loader на наш CTA ===
$cb = $modx->getObject('modChunk', ['name'=>'ContentBanner']);
if ($cb) {
    $cbBody = $cb->get('snippet');
    $newCb = <<<'HTML'
<div class="text-center my-4 my-md-5">
    <a href="#"
       class="btn btn-lg fw-7 py-3 px-4 d-inline-flex align-items-center gap-2 shadow-sm"
       style="background:#06ae1f;color:#fff;border-radius:8px;"
       data-bs-toggle="modal"
       data-bs-target="#MdGlNew"
       data-form-source="Контент-баннер блога: [[*pagetitle]]"
       data-form-title="Получить программу обучения"
       data-form-subtitle="Расскажем подробнее о подходящей программе, цене и сроках. Менеджер перезвонит в течение 15 минут.">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span>Получить программу обучения</span>
    </a>
</div>
HTML;
    if (trim($cbBody) !== trim($newCb)) {
        $totals['chunk']++;
        $diffs['chunk'][] = "chunk 'ContentBanner' id=".$cb->get('id').": replaced B24-loader with CTA (was ".strlen($cbBody).", now ".strlen($newCb).")";
        if ($APPLY) {
            $cb->set('snippet', $newCb);
            $cb->save();
        }
    }
}

// === 1. Шаблоны ===
$tplIds = [3, 5, 8, 14, 15, 21, 27];
foreach ($tplIds as $tid) {
    $t = $modx->getObject('modTemplate', $tid);
    if (!$t) continue;
    $orig = $t->get('content');
    $new = transform($orig);
    $deltaLen = strlen($new) - strlen($orig);
    if ($new !== $orig) {
        $totals['tpl']++;
        $diffs['tpl'][] = "tpl $tid '".$t->get('templatename')."': delta $deltaLen chars";
        if ($APPLY) {
            $t->set('content', $new);
            $t->save();
        }
    }
}

// === 2. Чанки ===
$chunkNames = [
    'Navbar', 'Footer', 'contAdres', 'msProduct.content.prod', 'boxManager',
    'boxComerch', 'catalogFooter', 'SaleCard.tpl', 'boxFormNews',
    'tpl.msProducts.row.Obr.Modal', 'catalogCard.tpl.modal',
    'catalogHeaderDPO', 'catalogFooterDPO', 'ContentBannerSout',
    'msProduct.content.PSK', 'boxCTABanner', 'boxGuaranteePSK',
    'msProduct.content.PSK_partner'
    // ContentBanner — обработан отдельно (полная очистка) выше
];
foreach ($chunkNames as $name) {
    $c = $modx->getObject('modChunk', ['name'=>$name]);
    if (!$c) continue;
    $orig = $c->get('snippet');
    $new = transform($orig);
    if ($new !== $orig) {
        $totals['chunk']++;
        $deltaLen = strlen($new) - strlen($orig);
        $diffs['chunk'][] = "chunk '$name' id=".$c->get('id').": delta $deltaLen chars";
        if ($APPLY) {
            $c->set('snippet', $new);
            $c->save();
        }
    }
}

// === 3. Ресурсы (только те 122 — из CSV) ===
$csv = dirname(__FILE__).'/_b24_cleanup_backup/resources_with_b24.csv';
if (file_exists($csv)) {
    $fp = fopen($csv, 'r');
    $hdr = fgetcsv($fp);
    while (($row = fgetcsv($fp)) !== false) {
        $rid = (int)$row[0];
        $r = $modx->getObject('modResource', $rid);
        if (!$r) continue;
        $orig = (string)$r->get('content');
        if ($orig === '') continue;
        $new = transform($orig);
        if ($new !== $orig) {
            $totals['res']++;
            if ($APPLY) {
                $r->set('content', $new);
                $r->save();
            }
        }
    }
    fclose($fp);
} else {
    echo "WARN: CSV $csv not found, skip resources\n";
}

echo "\n=== SUMMARY ===\n";
echo "templates changed: ".$totals['tpl']."\n";
foreach ($diffs['tpl'] as $d) echo "  $d\n";
echo "chunks changed: ".$totals['chunk']."\n";
foreach ($diffs['chunk'] as $d) echo "  $d\n";
echo "resources changed: ".$totals['res']."\n";

if ($DRY) {
    echo "\nDRY mode — nothing written. Run with --apply to apply.\n";
} else {
    $modx->cacheManager->refresh();
    echo "\nCache refreshed.\n";
}
