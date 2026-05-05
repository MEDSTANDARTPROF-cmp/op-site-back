<?php
/**
 * Переключаем оставшиеся CTA на карточках рабочих профессий:
 *  1. Hero "Узнать подробности" (в TV offerDescription у эталона id=7076) → микроформа
 *  2. Программа "Запросить актуальную программу" (в TV cont2Prog у 1615 страниц) → новая модалка
 */
$db = new PDO('mysql:host=localhost;dbname=obrprofi', 'obrprofi', 'obrprofi');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// === 1. HERO: TV offerDescription у эталона 7076 ===
echo "=== 1. Hero offerDescription (эталон id=7076) ===\n";
$tv_id = $db->query("SELECT id FROM modx_site_tmplvars WHERE name='offerDescription'")->fetchColumn();
$row = $db->query("SELECT cv.id, cv.value FROM modx_site_tmplvar_contentvalues cv WHERE cv.contentid=7076 AND cv.tmplvarid=$tv_id")->fetch(PDO::FETCH_ASSOC);

$old_btn_pattern = '/<a\s+href="#"\s+class="btn b24-form-marker"\s+data-bs-toggle="modal"\s+data-bs-target="#MdGl"[^>]*>\s*Узнать подробности\s*<\/a>/u';
$old_value = $row['value'];

if (!preg_match($old_btn_pattern, $old_value)) {
    if (strpos($old_value, 'ObrFormMini') !== false) {
        echo "[=] Уже содержит ObrFormMini, пропускаю\n";
    } else {
        echo "[!] Кнопка не найдена паттерном. Содержимое:\n" . $old_value . "\n";
    }
} else {
    // Заменяем кнопку на микроформу
    $new_value = preg_replace($old_btn_pattern, '[[$ObrFormMini]]', $old_value, 1);
    $st = $db->prepare("UPDATE modx_site_tmplvar_contentvalues SET value=:v WHERE id=" . (int)$row['id']);
    $st->execute([':v' => $new_value]);
    echo "[+] Hero offerDescription обновлён — кнопка заменена на [[\$ObrFormMini]]\n";
    echo "Затронуто страниц: 1615 (все рабочие профессии)\n";
}

// === 2. cont2Prog: массовая замена «Запросить актуальную программу» в 1615 значениях ===
echo "\n=== 2. cont2Prog: «Запросить актуальную программу» → новая модалка ===\n";
$tv_id_cont = $db->query("SELECT id FROM modx_site_tmplvars WHERE name='cont2Prog'")->fetchColumn();

// Шаблон старой кнопки
$old_btn_pattern2 = '/<a\s+onclick="ym\(75081295,\'reachGoal\',\'zayavka\'\)"\s+href="#"\s+class="btn btn-sm btn-outline-secondary b24-form-marker"\s+data-bs-toggle="modal"\s+data-marker="Запросить программу"\s+data-bs-target="#MdGl">Запросить актуальную программу<\/a>/u';

$new_btn = '<a onclick="ym(75081295,\'reachGoal\',\'zayavka\')" href="#" class="btn btn-sm btn-outline-secondary"
   data-bs-toggle="modal" data-bs-target="#MdGlNew"
   data-form-title="Запросить актуальную программу"
   data-form-subtitle="Менеджер пришлёт программу обучения и расчёт стоимости в течение 15 минут"
   data-form-source="Карточка рабочей профессии: [[*pagetitle]] (запрос программы)">Запросить актуальную программу</a>';

$rows = $db->query("
SELECT cv.id, cv.contentid, cv.value FROM modx_site_tmplvar_contentvalues cv
JOIN modx_site_content c ON cv.contentid=c.id
WHERE cv.tmplvarid=$tv_id_cont AND c.template=26 AND c.deleted=0 AND c.published=1
")->fetchAll(PDO::FETCH_ASSOC);

echo "Всего записей TV cont2Prog: " . count($rows) . "\n";

$updated = 0;
$st = $db->prepare("UPDATE modx_site_tmplvar_contentvalues SET value=:v WHERE id=:id");

foreach ($rows as $r) {
    $val = $r['value'];
    if (strpos($val, 'MdGlNew') !== false) continue; // уже переключена
    if (!preg_match($old_btn_pattern2, $val)) continue;

    $new_val = preg_replace($old_btn_pattern2, $new_btn, $val, 1);
    $st->execute([':v' => $new_val, ':id' => $r['id']]);
    $updated++;
}

echo "[+] Обновлено: $updated TV-значений\n";

echo "\nГотово. Не забыть очистить кеш MODX.\n";
