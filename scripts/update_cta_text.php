<?php
/**
 * Меняет текст hero-CTA на пилотной категории и заголовок модалки.
 * "Подобрать курс с ценой" -> "Подобрать курс"
 * "Подобрать курс по направлению «...»" -> "Подбор курса"
 */
$db = new PDO("mysql:host=localhost;dbname=obrprofi", "obrprofi", "obrprofi");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$row = $db->query("SELECT snippet FROM modx_site_htmlsnippets WHERE id=47")->fetch(PDO::FETCH_ASSOC);
$header = $row['snippet'];

$replacements = [
    'data-form-title="Подобрать курс по направлению «[[*pagetitle]]»"' => 'data-form-title="Подбор курса"',
    '<span>Подобрать курс с ценой</span>' => '<span>Подобрать курс</span>',
];

$count = 0;
foreach ($replacements as $old => $new) {
    if (strpos($header, $old) !== false) {
        $header = str_replace($old, $new, $header);
        echo "[+] Заменил: $old -> $new\n";
        $count++;
    } else {
        echo "[!] Не найдено: $old\n";
    }
}

if ($count > 0) {
    $st = $db->prepare("UPDATE modx_site_htmlsnippets SET snippet=:c WHERE id=47");
    $st->execute([':c' => $header]);
    echo "[OK] catalogHeader обновлён\n";
} else {
    echo "Нечего обновлять\n";
}
