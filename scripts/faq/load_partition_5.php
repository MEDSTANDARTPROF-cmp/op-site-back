<?php
define('MODX_API_MODE', true);
require_once __DIR__ . '/../../site/index.php';

$items = [
    [46, 'faq_pk_root.json',          'seo_pk_root.html',          'ПК (корневая)'],
    [61, 'faq_pp_root.json',          'seo_pp_root.html',          'ПП (корневая)'],
    [44, 'faq_attestaciya_root.json', 'seo_attestaciya_root.html', 'Аттестация (корневая)'],
    [63, 'faq_rs_root.json',          'seo_rs_root.html',          'РС (корневая)'],
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
