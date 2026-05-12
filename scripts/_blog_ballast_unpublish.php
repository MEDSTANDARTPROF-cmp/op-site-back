<?php
/**
 * Распубликовать 299 балластных блог-статей.
 * Берёт список URL из temp_back/blog_audit/blog_балласт.csv, находит ресурсы
 * по uri, ставит published=0 + clear pub_date/unpub_date (на случай auto-publish).
 *
 * Также генерирует:
 *  - scripts/_blog_ballast_ids.txt — список ID для документации
 *  - ОБРПРОФИ_АНАЛИТИКА/blog_ballast_urls.txt — список URL для Я.Вебмастер
 *
 * Usage:
 *   php scripts/_blog_ballast_unpublish.php --dry    # без записи
 *   php scripts/_blog_ballast_unpublish.php --apply  # применить
 */
define('MODX_API_MODE', true);
$idx = dirname(__DIR__).'/site/index.php';
if (!file_exists($idx)) $idx = dirname(__DIR__).'/index.php';
require_once $idx;

$DRY = in_array('--dry', $argv);
$APPLY = in_array('--apply', $argv);
if (!$DRY && !$APPLY) { echo "Usage: php scripts/_blog_ballast_unpublish.php --dry | --apply\n"; exit(1); }

$csv = dirname(__DIR__).'/temp_back/blog_audit/blog_балласт.csv';
if (!file_exists($csv)) { echo "CSV not found: $csv\n"; exit(1); }

$urls = [];
$fp = fopen($csv, 'r');
fgetcsv($fp);
while (($row = fgetcsv($fp)) !== false) {
    $url = $row[0];
    $url = ltrim($url, '/'); // 'blog/...' (uri MODX без leading /)
    $urls[] = $url;
}
fclose($fp);
echo "URLs in CSV: ".count($urls)."\n";

$ids = [];
$not_found = [];
$already_unpublished = [];
$applied = 0;

foreach ($urls as $url) {
    $r = $modx->getObject('modResource', ['uri'=>$url]);
    if (!$r) { $not_found[] = $url; continue; }
    if (!$r->get('published')) {
        $already_unpublished[] = $r->get('id');
        $ids[] = $r->get('id');
        continue;
    }
    $ids[] = $r->get('id');
    if ($APPLY) {
        $r->set('published', 0);
        $r->set('publishedon', 0);
        $r->set('pub_date', 0);
        $r->set('unpub_date', 0);
        $r->save();
        $applied++;
    }
}

echo "Resources found: ".count($ids)."\n";
echo "Already unpublished: ".count($already_unpublished)."\n";
echo "Not found in DB: ".count($not_found)."\n";
if ($not_found) {
    echo "  examples:\n";
    foreach (array_slice($not_found, 0, 5) as $u) echo "    $u\n";
}

if ($APPLY) {
    echo "\nApplied: $applied resources set published=0\n";
    // Сохранить список ID
    file_put_contents(__DIR__.'/_blog_ballast_ids.txt',
        "# 299 балластных блог-статей, распубликованы 2026-05-12\n".
        "# При синхах БД эти IDs должны оставаться published=0\n".
        "# Источник: temp_back/blog_audit/blog_балласт.csv\n\n".
        implode("\n", $ids)."\n"
    );
    echo "IDs list saved: scripts/_blog_ballast_ids.txt\n";

    // Сохранить URL-список для Я.Вебмастер
    $webmaster_path = dirname(__DIR__).'/ОБРПРОФИ_АНАЛИТИКА/blog_ballast_urls.txt';
    $wm_lines = [];
    foreach ($urls as $url) {
        $wm_lines[] = 'https://obrprofi.ru/'.$url;
    }
    file_put_contents($webmaster_path,
        implode("\n", $wm_lines)."\n"
    );
    echo "Webmaster URL list saved: ОБРПРОФИ_АНАЛИТИКА/blog_ballast_urls.txt\n";

    $modx->cacheManager->refresh();
    echo "Cache refreshed.\n";
} else {
    echo "\nDRY mode — nothing written.\n";
}
