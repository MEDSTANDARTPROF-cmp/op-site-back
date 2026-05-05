<?php
/**
 * Переключаем CTA-кнопки в карточках рабочих профессий на новую модалку #MdGlNew
 * (затрагивает 1615 страниц, использующих чанк msProduct.content.PSK).
 *
 * Сохраняем ym('zayavka') для совместимости со старой целью «Заявка (shems)».
 * Добавляем data-form-* атрибуты с контекстом.
 */
$db = new PDO('mysql:host=localhost;dbname=obrprofi', 'obrprofi', 'obrprofi');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$row = $db->query("SELECT snippet FROM modx_site_htmlsnippets WHERE id=102")->fetch(PDO::FETCH_ASSOC);
$content = $row['snippet'];

if (strpos($content, 'MdGlNew') !== false) {
    echo "[=] Уже переключено на MdGlNew\n";
    exit;
}

$replacements = [
    // CTA "Консультация с менеджером" — мягкий вопрос, шапка карточки
    'data-bs-toggle="modal" data-marker="Консультация товар" data-bs-target="#MdGl"' =>
        'data-bs-toggle="modal" data-bs-target="#MdGlNew" data-form-title="Заявка на курс «[[*pagetitle]]»" data-form-subtitle="Расскажем подробнее о программе, цене и сроках. Менеджер свяжется в течение 15 минут." data-form-source="Карточка рабочей профессии: [[*pagetitle]]"',

    // CTA "Записаться на курс" — твёрдое действие
    'data-bs-toggle="modal" data-marker="Записаться о курсе" data-bs-target="#MdGl"' =>
        'data-bs-toggle="modal" data-bs-target="#MdGlNew" data-form-title="Записаться на курс «[[*pagetitle]]»" data-form-subtitle="Менеджер свяжется в течение 15 минут, оформит документы и расскажет о ближайшем старте." data-form-source="Карточка рабочей профессии: [[*pagetitle]] (запись)"',
];

$count = 0;
foreach ($replacements as $old => $new) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        echo "[+] Переключил: " . substr($old, -100) . "\n";
        $count++;
    }
}

if ($count > 0) {
    $st = $db->prepare("UPDATE modx_site_htmlsnippets SET snippet=:c WHERE id=102");
    $st->execute([':c' => $content]);
    echo "\n[OK] msProduct.content.PSK обновлён ($count правок)\n";
    echo "Затронуто: 1615 страниц рабочих профессий\n";
}
