<?php
/**
 * Регистрация в MODX элементов формы ObrForm:
 *   - Категория ObrForm
 *   - Сниппет sendLeadToB24 (статичный, привязан к файлу)
 *   - Чанк ObrForm.modalForm (статичный, привязан к файлу)
 *   - Ресурс /ajax-lead — вызывает сниппет, отдаёт JSON
 *
 * Идемпотентный — повторный запуск не создаёт дубликатов.
 *
 * Запуск: php scripts/setup_obr_form.php
 */

define('MODX_API_MODE', true);
require_once __DIR__ . '/../site/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->setOption('cache_db', false);

echo "=== Setup ObrForm ===\n";

// 1. Категория
$category = $modx->getObject('modCategory', ['category' => 'ObrForm']);
if (!$category) {
    $category = $modx->newObject('modCategory');
    $category->set('category', 'ObrForm');
    $category->save();
    echo "[+] Категория ObrForm создана (id={$category->get('id')})\n";
} else {
    echo "[=] Категория ObrForm уже есть (id={$category->get('id')})\n";
}
$category_id = $category->get('id');

// 2. Сниппет sendLeadToB24
$snippet = $modx->getObject('modSnippet', ['name' => 'sendLeadToB24']);
$snippet_file = 'core/elements/snippets/sendLeadToB24.php';
$snippet_full_path = MODX_BASE_PATH . $snippet_file;

if (!file_exists($snippet_full_path)) {
    die("[!] Файл сниппета не найден: $snippet_full_path\n");
}

if (!$snippet) {
    $snippet = $modx->newObject('modSnippet');
    $snippet->set('name', 'sendLeadToB24');
    $snippet->set('description', 'Приём AJAX-заявки и проброс в Битрикс24 через REST API');
    $snippet->set('category', $category_id);
    $snippet->set('static', true);
    $snippet->set('source', 1); // default media source (filesystem)
    $snippet->set('static_file', $snippet_file);
    $snippet->save();
    echo "[+] Сниппет sendLeadToB24 создан (id={$snippet->get('id')})\n";
} else {
    $snippet->set('static', true);
    $snippet->set('source', 1);
    $snippet->set('static_file', $snippet_file);
    $snippet->set('category', $category_id);
    $snippet->save();
    echo "[=] Сниппет sendLeadToB24 уже есть, обновлён (id={$snippet->get('id')})\n";
}

// 3. Чанк ObrForm.modalForm
$chunk = $modx->getObject('modChunk', ['name' => 'ObrForm.modalForm']);
$chunk_file = 'core/components/chunks/ObrForm/modalForm.tpl';
$chunk_full_path = MODX_BASE_PATH . $chunk_file;

if (!file_exists($chunk_full_path)) {
    die("[!] Файл чанка не найден: $chunk_full_path\n");
}

if (!$chunk) {
    $chunk = $modx->newObject('modChunk');
    $chunk->set('name', 'ObrForm.modalForm');
    $chunk->set('description', 'HTML-разметка модальной формы ObrForm');
    $chunk->set('category', $category_id);
    $chunk->set('static', true);
    $chunk->set('source', 1);
    $chunk->set('static_file', $chunk_file);
    $chunk->save();
    echo "[+] Чанк ObrForm.modalForm создан (id={$chunk->get('id')})\n";
} else {
    $chunk->set('static', true);
    $chunk->set('source', 1);
    $chunk->set('static_file', $chunk_file);
    $chunk->set('category', $category_id);
    $chunk->save();
    echo "[=] Чанк ObrForm.modalForm уже есть, обновлён (id={$chunk->get('id')})\n";
}

// 4. Ресурс ajax-lead
$resource = $modx->getObject('modResource', ['alias' => 'ajax-lead', 'parent' => 0]);

// Найдём шаблон без обёртки. Предпочитаем clean-template если есть, иначе создадим.
$clean_template = $modx->getObject('modTemplate', ['templatename' => 'clean-template']);
if (!$clean_template) {
    $clean_template = $modx->newObject('modTemplate');
    $clean_template->set('templatename', 'clean-template');
    $clean_template->set('description', 'Минимальный шаблон без обёртки — для AJAX-эндпоинтов');
    $clean_template->set('content', '[[*content]]');
    $clean_template->set('category', $category_id);
    $clean_template->save();
    echo "[+] Шаблон clean-template создан (id={$clean_template->get('id')})\n";
} else {
    echo "[=] Шаблон clean-template уже есть (id={$clean_template->get('id')})\n";
}
$template_id = $clean_template->get('id');

if (!$resource) {
    $resource = $modx->newObject('modResource');
    $resource->set('pagetitle', 'AJAX Lead Endpoint');
    $resource->set('alias', 'ajax-lead');
    $resource->set('parent', 0);
    $resource->set('template', $template_id);
    $resource->set('content', '[[!sendLeadToB24]]');
    $resource->set('content_type', 1); // 1 = HTML обычно. content_dispositon мы перебиваем в сниппете.
    $resource->set('class_key', 'modDocument');
    $resource->set('context_key', 'web');
    $resource->set('published', true);
    $resource->set('hidemenu', true);
    $resource->set('searchable', false);
    $resource->set('cacheable', false);
    $resource->set('richtext', false);
    $resource->set('uri', 'ajax-lead');
    $resource->set('uri_override', true);
    $resource->save();
    echo "[+] Ресурс /ajax-lead создан (id={$resource->get('id')})\n";
} else {
    $resource->set('template', $template_id);
    $resource->set('content', '[[!sendLeadToB24]]');
    $resource->set('published', true);
    $resource->set('hidemenu', true);
    $resource->set('searchable', false);
    $resource->set('cacheable', false);
    $resource->set('richtext', false);
    $resource->save();
    echo "[=] Ресурс /ajax-lead уже есть, обновлён (id={$resource->get('id')})\n";
}

// 5. Чистим кеш
$modx->cacheManager->refresh();
echo "[+] Кеш MODX очищен\n";

echo "\n=== Готово ===\n";
echo "Endpoint: " . MODX_SITE_URL . "ajax-lead\n";
echo "Чанк для вывода в шаблон: [[\$ObrForm.modalForm]]\n";
