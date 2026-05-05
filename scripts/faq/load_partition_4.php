<?php
define('MODX_API_MODE', true);
require_once __DIR__ . '/../../site/index.php';

$items = [
    [53,  'faq_radiatsionnaya_pk.json', 'seo_radiatsionnaya_pk.html', 'ПК Радиационная безопасность'],
    [54,  'faq_teploenergetika_pk.json','seo_teploenergetika_pk.html','ПК Теплоэнергетика'],
    [50,  'faq_gostinichnoe_pk.json',   'seo_gostinichnoe_pk.html',   'ПК Гостиничное дело'],
    [295, 'faq_sout.json',              'seo_sout.html',              'СОУТ'],
    [60,  'faq_sertifikatsiya.json',    'seo_sertifikatsiya.html',    'Сертификация'],
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
