<?php
define('MODX_API_MODE', true);
require_once __DIR__ . '/../../site/index.php';

$items = [
    [59, 'faq_stroitelstvo_pk.json',     'seo_stroitelstvo_pk.html',     'ПК Строительство'],
    [57, 'faq_pervaya_pomoshch_pk.json', 'seo_pervaya_pomoshch_pk.html', 'ПК Первая помощь'],
    [56, 'faq_go_chs_pk.json',           'seo_go_chs_pk.html',           'ПК ГО ЧС'],
    [52, 'faq_ekologiya_pk.json',        'seo_ekologiya_pk.html',        'ПК Экология'],
    [51, 'faq_transportnaya_pk.json',    'seo_transportnaya_pk.html',    'ПК Транспортная'],
];

foreach ($items as [$rid, $faq_file, $seo_file, $name]) {
    $res = $modx->getObject('modResource', $rid);
    if (!$res) { echo "[!] $rid ($name) — не найден\n"; continue; }
    $faq = file_get_contents(__DIR__ . '/' . $faq_file);
    $seo = file_get_contents(__DIR__ . '/' . $seo_file);
    if (!$faq || !$seo) { echo "[!] $rid ($name) — файлы не найдены\n"; continue; }
    $res->setTVValue('faq_data', $faq);
    $res->setTVValue('seo_article', $seo);
    echo "[+] $rid ($name) — загружено\n";
}

$modx->cacheManager->refresh();
echo "\n[+] Кеш MODX очищен\n";
