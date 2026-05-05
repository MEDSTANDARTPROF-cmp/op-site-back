<?php
/**
 * Загрузка Партии 2: 5 категорий
 *  - ПП Охрана труда (id=65)
 *  - ПП Промышленная безопасность (id=64)
 *  - ПП Пожарная безопасность (id=3727)
 *  - Аттестация ОТ (id=45)
 *  - Аттестация ЭБ (id=48)
 */
define('MODX_API_MODE', true);
require_once __DIR__ . '/../../site/index.php';

$items = [
    [65,   'faq_pp_okhrana_truda.json',                'seo_pp_okhrana_truda.html',                'ПП Охрана труда'],
    [64,   'faq_pp_promyshlennaya_bezopasnost.json',   'seo_pp_promyshlennaya_bezopasnost.html',   'ПП Промбез'],
    [3727, 'faq_pp_pozharnaya.json',                   'seo_pp_pozharnaya.html',                   'ПП Пожарная'],
    [45,   'faq_attestaciya_ot.json',                  'seo_attestaciya_ot.html',                  'Аттестация ОТ'],
    [48,   'faq_attestaciya_eb.json',                  'seo_attestaciya_eb.html',                  'Аттестация ЭБ'],
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
