<?php
/**
 * Обновляет hero-CTA на категориях курсов:
 *   - текст: "Подобрать курс" -> "Подобрать программу"
 *   - стиль: white-with-border -> primary заливка (заметнее)
 *   - data-form-title: "Подбор курса" -> [[*offerH1:default=`[[*pagetitle]]`]] (короткий H1 страницы)
 *   - data-form-subtitle: уточнить
 */
$db = new PDO("mysql:host=localhost;dbname=obrprofi", "obrprofi", "obrprofi");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$row = $db->query("SELECT snippet FROM modx_site_htmlsnippets WHERE id=47")->fetch(PDO::FETCH_ASSOC);
$header = $row['snippet'];

$replacements = [
    // 1. Текст кнопки
    '<span>Подобрать курс</span>' => '<span>Подобрать программу <span class="ms-1">→</span></span>',

    // 2. Заголовок модалки — H1 страницы (короткий)
    'data-form-title="Подбор курса"' => 'data-form-title="[[*offerH1:default=`[[*pagetitle]]`]]"',

    // 3. Подзаголовок (мелким текстом под заголовком модалки)
    'data-form-subtitle="С расчётом стоимости и сроков. Менеджер свяжется в течение 15 минут."'
        => 'data-form-subtitle="Подберём программу с расчётом стоимости и сроков. Менеджер свяжется в течение 15 минут."',

    // 4. Стиль кнопки — primary (синий) заметнее, без перебора
    'class="bg-white border border-2 border-primary btn btn-ic fw-6 mt-4 pe-3 py-2"'
        => 'class="btn btn-ic fw-6 mt-4 pe-4 py-3 obr-hero-cta"',

    // 5. Inline style — убрать (новый стиль через класс)
    '                       style="color: #06ae1f;">' => '>',

    // 6. Иконка — фильтр уже не нужен (фон тёмный)
    'style="filter: brightness(1.5);"' => '',
];

$count = 0;
foreach ($replacements as $old => $new) {
    if (strpos($header, $old) !== false) {
        $header = str_replace($old, $new, $header);
        echo "[+] " . substr($old, 0, 70) . "...\n";
        $count++;
    } else {
        echo "[!] Не найдено: " . substr($old, 0, 70) . "...\n";
    }
}

if ($count > 0) {
    $st = $db->prepare("UPDATE modx_site_htmlsnippets SET snippet=:c WHERE id=47");
    $st->execute([':c' => $header]);
    echo "\n[OK] catalogHeader обновлён ($count правок)\n";
} else {
    echo "\nНечего обновлять\n";
}
