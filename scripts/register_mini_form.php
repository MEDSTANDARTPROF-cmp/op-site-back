<?php
/**
 * Регистрация чанка ObrFormMini в MODX (статичный, привязан к файлу)
 * + замена кнопки «Подобрать программу» в catalogHeader на вызов микроформы.
 */
define('MODX_API_MODE', true);
require_once __DIR__ . '/../site/index.php';

$modx->setLogTarget('ECHO');
$modx->setOption('cache_db', false);

echo "=== Регистрация микроформы ===\n";

// Категория уже есть (ObrForm id=32)
$category = $modx->getObject('modCategory', ['category' => 'ObrForm']);
$category_id = $category ? $category->get('id') : 0;
if (!$category_id) {
    die("[!] Категория ObrForm не найдена. Сначала запустите setup_obr_form.php\n");
}

// Чанк ObrFormMini
$chunk_file = 'core/components/chunks/ObrForm/miniForm.tpl';
$chunk_full = MODX_BASE_PATH . $chunk_file;
if (!file_exists($chunk_full)) {
    die("[!] Файл чанка не найден: $chunk_full\n");
}

$chunk = $modx->getObject('modChunk', ['name' => 'ObrFormMini']);
if (!$chunk) {
    $chunk = $modx->newObject('modChunk');
    $chunk->set('name', 'ObrFormMini');
    $chunk->set('description', 'Микроформа на hero-блоке (1 поле — телефон)');
    $chunk->set('category', $category_id);
    $chunk->set('static', true);
    $chunk->set('source', 1);
    $chunk->set('static_file', $chunk_file);
    $chunk->save();
    echo "[+] Чанк ObrFormMini создан (id={$chunk->get('id')})\n";
} else {
    $chunk->set('static', true);
    $chunk->set('source', 1);
    $chunk->set('static_file', $chunk_file);
    $chunk->save();
    echo "[=] Чанк ObrFormMini уже есть, обновлён (id={$chunk->get('id')})\n";
}

// === Замена кнопки в catalogHeader ===
echo "\n=== Замена hero-CTA в catalogHeader (id=47) ===\n";

$pdo = new PDO('mysql:host=localhost;dbname=obrprofi;charset=utf8', 'obrprofi', 'obrprofi');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$row = $pdo->query("SELECT snippet FROM modx_site_htmlsnippets WHERE id=47")->fetch(PDO::FETCH_ASSOC);
$header = $row['snippet'];

if (strpos($header, 'ObrFormMini') !== false) {
    echo "[=] Микроформа уже подключена\n";
} else {
    // Старый блок hero-CTA — обернутый в div с CTA-кнопкой и закомм. WhatsApp
    $old_pattern = '/<div class="align-items-baseline d-flex flex-column pb-5"[^>]*>.*?<\/div>\s*<br/su';

    $new_html = '<div class="align-items-baseline d-flex flex-column pb-5">
                        [[$ObrFormMini]]
                    </div>
                    <br';

    if (preg_match($old_pattern, $header)) {
        $header = preg_replace($old_pattern, $new_html, $header, 1);
        $st = $pdo->prepare("UPDATE modx_site_htmlsnippets SET snippet=:c WHERE id=47");
        $st->execute([':c' => $header]);
        echo "[+] Кнопка «Подобрать программу» заменена на [[\$ObrFormMini]]\n";
    } else {
        echo "[!] Не найден старый CTA-блок. Откатываю автозамену.\n";
        echo "Поищу контекст:\n";
        if (preg_match('/(.{80}Подобрать программу.{200})/su', $header, $m)) {
            echo $m[1] . "\n";
        }
    }
}

// Очистка кеша
$modx->cacheManager->refresh();
echo "\n[+] Кеш MODX очищен\n";
echo "\nГотово. Открой страницу категории и проверь — на hero вместо кнопки должна быть микроформа.\n";
