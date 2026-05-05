<?php
/**
 * Обновляет содержимое чанков Footer и catalogHeader в БД
 * (вписывает в них правки которые мы делали в файлах).
 *
 * Действия:
 * 1. Footer (id=3): добавляет [[$ObrFormModal]] перед глобальной модалкой #MdGl
 * 2. catalogHeader (id=47): меняет hero-CTA на новую модалку MdGlNew + контекст через data-form-*
 */
$db = new PDO("mysql:host=localhost;dbname=obrprofi", "obrprofi", "obrprofi");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// === Footer ===
echo "=== Footer (id=3) ===\n";
$row = $db->query("SELECT snippet FROM modx_site_htmlsnippets WHERE id=3")->fetch(PDO::FETCH_ASSOC);
$footer = $row['snippet'];

if (strpos($footer, 'ObrFormModal') !== false) {
    echo "[=] Уже содержит ObrFormModal, пропускаю\n";
} else {
    $needle = '<!-- Глобальная форма -->';
    if (strpos($footer, $needle) !== false) {
        $insert = "<!-- Новая глобальная форма (ObrForm) -->\n[[\$ObrFormModal]]\n\n<!-- Глобальная форма (старая, B24) -->";
        $footer_new = str_replace($needle, $insert, $footer);
        echo "[+] Найден маркер 'Глобальная форма', вставляю чанк\n";
    } else {
        // Запасной план — если маркера нет, найдём по началу div id="MdGl"
        $needle2 = '<div class="modal fade" id="MdGl"';
        if (strpos($footer, $needle2) !== false) {
            $insert = "<!-- Новая глобальная форма (ObrForm) -->\n[[\$ObrFormModal]]\n\n<!-- Глобальная форма (старая, B24) -->\n" . $needle2;
            $footer_new = str_replace($needle2, $insert, $footer);
            echo "[+] Маркер 'Глобальная форма' не найден — вставляю перед <div id=MdGl>\n";
        } else {
            echo "[!] Не найден ни маркер, ни модалка #MdGl. Дамп первых 500 символов:\n";
            echo substr($footer, 0, 500) . "\n...\n";
            echo "Прерываю.\n";
            exit(1);
        }
    }

    $st = $db->prepare("UPDATE modx_site_htmlsnippets SET snippet=:c WHERE id=3");
    $st->execute([':c' => $footer_new]);
    echo "[OK] Footer обновлён\n";
}

// === catalogHeader ===
echo "\n=== catalogHeader (id=47) ===\n";
$row = $db->query("SELECT snippet FROM modx_site_htmlsnippets WHERE id=47")->fetch(PDO::FETCH_ASSOC);
$header = $row['snippet'];

if (strpos($header, 'MdGlNew') !== false) {
    echo "[=] Уже содержит MdGlNew, пропускаю\n";
} else {
    // Ищем старый Hero CTA с маркером "Консультация  с менеджером - шапка сайта"
    $old_pattern = '/<a onclick="ym\(75081295,\'reachGoal\',\'zayavka\'\)" href="#" class="b24-form-marker bg-white border border-2 border-primary btn btn-ic fw-6 mt-4 pe-3 py-2" data-marker="Консультация  с менеджером - шапка сайта" data-bs-toggle="modal" data-bs-target="#MdGl"[^>]*>.*?<\/a>/su';
    if (preg_match($old_pattern, $header)) {
        $new_html = '<a onclick="ym(75081295,\'reachGoal\',\'zayavka\')" href="#" class="bg-white border border-2 border-primary btn btn-ic fw-6 mt-4 pe-3 py-2"
                       data-bs-toggle="modal"
                       data-bs-target="#MdGlNew"
                       data-form-title="Подобрать курс по направлению «[[*pagetitle]]»"
                       data-form-subtitle="С расчётом стоимости и сроков. Менеджер свяжется в течение 15 минут."
                       data-form-source="Категория курсов: [[*pagetitle]]"
                       style="color: #06ae1f;">
                      <img src="assets/images/temp/ic-mess-32.svg" width="32" height="32" alt="Консультация с менеджером" class="" style="filter: brightness(1.5);">
                      <span>Подобрать курс с ценой</span>
                    </a>';
        $header_new = preg_replace($old_pattern, $new_html, $header);
        $st = $db->prepare("UPDATE modx_site_htmlsnippets SET snippet=:c WHERE id=47");
        $st->execute([':c' => $header_new]);
        echo "[OK] catalogHeader обновлён\n";
    } else {
        echo "[!] Не найден старый Hero CTA по паттерну. Дамп фрагмента:\n";
        // Найдём контекст
        if (preg_match('/(.{100}Консультация.{200})/su', $header, $m)) {
            echo $m[1] . "\n";
        } else {
            echo substr($header, 0, 500) . "\n";
        }
        echo "Прерываю — нужно скорректировать паттерн.\n";
    }
}

echo "\nГотово. Не забыть очистить кеш MODX.\n";
