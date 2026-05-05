<?php
/**
 * Когда мы загружаем новый seo_article на категорию — нужно очистить старый cont2faqNew,
 * чтобы не было дубля на странице.
 *
 * Скрипт идемпотентный:
 *  1) Архивирует старое значение cont2faqNew в JSON-файл (бэкап на случай отката)
 *  2) Очищает cont2faqNew у тех ресурсов, где есть наш новый seo_article
 *
 * Безопасен для остальных страниц (карточки товаров, рабочих) — там cont2faqNew не трогается.
 */
$db = new PDO('mysql:host=localhost;dbname=obrprofi', 'obrprofi', 'obrprofi');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tv_cont = $db->query("SELECT id FROM modx_site_tmplvars WHERE name='cont2faqNew'")->fetchColumn();
$tv_seo  = $db->query("SELECT id FROM modx_site_tmplvars WHERE name='seo_article'")->fetchColumn();

if (!$tv_cont || !$tv_seo) die("[!] TV не найдены\n");

// Находим ресурсы где есть наш seo_article И есть старый cont2faqNew
$rows = $db->query("
    SELECT
        c.id, c.pagetitle, c.alias,
        cv_seo.value AS seo_value,
        cv_cont.id AS cont_value_id, cv_cont.value AS cont_value
    FROM modx_site_content c
    JOIN modx_site_tmplvar_contentvalues cv_seo ON cv_seo.contentid=c.id AND cv_seo.tmplvarid=$tv_seo
    JOIN modx_site_tmplvar_contentvalues cv_cont ON cv_cont.contentid=c.id AND cv_cont.tmplvarid=$tv_cont
    WHERE cv_seo.value != '' AND cv_cont.value != ''
")->fetchAll(PDO::FETCH_ASSOC);

echo "Найдено страниц с дублем (есть и seo_article, и cont2faqNew): " . count($rows) . "\n\n";

if (empty($rows)) { echo "Ничего чистить не надо.\n"; exit; }

// Архивируем
$backup = [];
foreach ($rows as $r) {
    $backup[] = [
        'id' => (int)$r['id'],
        'alias' => $r['alias'],
        'pagetitle' => $r['pagetitle'],
        'cont2faqNew_value' => $r['cont_value'],
    ];
    echo "  - id={$r['id']} ({$r['alias']}) — длина старого cont2faqNew: " . mb_strlen($r['cont_value']) . " символов\n";
}
$backup_file = __DIR__ . '/cont2faqNew_backup_' . date('Ymd_His') . '.json';
file_put_contents($backup_file, json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "\n[OK] Бэкап сохранён: $backup_file\n";

// Очищаем
$st = $db->prepare("UPDATE modx_site_tmplvar_contentvalues SET value='' WHERE id=:id");
foreach ($rows as $r) {
    $st->execute([':id' => $r['cont_value_id']]);
}
echo "[OK] Очищено cont2faqNew на " . count($rows) . " страницах\n";

// Очищаем кеш MODX
$cache_dir = __DIR__ . '/../../site/core/cache';
if (is_dir($cache_dir)) {
    foreach (glob($cache_dir . '/[a-z]*', GLOB_ONLYDIR) as $d) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $f) { $f->isDir() ? rmdir($f->getRealPath()) : unlink($f->getRealPath()); }
        rmdir($d);
    }
    echo "[OK] Кеш MODX очищен\n";
}
