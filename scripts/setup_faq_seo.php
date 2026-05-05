<?php
/**
 * Регистрация в MODX:
 *   - TV faq_data (textarea, JSON)
 *   - TV seo_article (richtext, HTML)
 *   - Сниппет renderFaq (статичный)
 *   - Чанк FaqBlock (статичный)
 *   - Чанк SeoArticleBlock (статичный)
 *   - Привязка TV к шаблонам категорий каталога
 *   - Подключение чанков в шаблоны категорий
 *   - Заполнение TV для пилотной страницы id=55 (Пожарная ПК)
 */
define('MODX_API_MODE', true);
require_once __DIR__ . '/../site/index.php';
$modx->setLogTarget('ECHO');
$modx->setOption('cache_db', false);

echo "=== Setup FAQ + SEO infrastructure ===\n\n";

$category = $modx->getObject('modCategory', ['category' => 'ObrForm']);
$category_id = $category ? $category->get('id') : 0;
if (!$category_id) {
    die("[!] Категория ObrForm не найдена\n");
}

$catalog_template_ids = [3, 5, 14, 15, 16, 23, 24, 27];

// === 1. TV faq_data ===
$tv_faq = $modx->getObject('modTemplateVar', ['name' => 'faq_data']);
if (!$tv_faq) {
    $tv_faq = $modx->newObject('modTemplateVar');
    $tv_faq->set('name', 'faq_data');
    $tv_faq->set('caption', 'FAQ (JSON)');
    $tv_faq->set('description', 'JSON массив {items:[{q,a},...]} — источник для FAQBlock. Пусто = блок не выводится.');
    $tv_faq->set('type', 'textarea');
    $tv_faq->set('category', $category_id);
    $tv_faq->set('input_properties', '{"rows":"15","columns":"50"}');
    $tv_faq->save();
    echo "[+] TV faq_data создан (id={$tv_faq->get('id')})\n";
} else {
    echo "[=] TV faq_data уже есть (id={$tv_faq->get('id')})\n";
}
$tv_faq_id = $tv_faq->get('id');

// === 2. TV seo_article ===
$tv_seo = $modx->getObject('modTemplateVar', ['name' => 'seo_article']);
if (!$tv_seo) {
    $tv_seo = $modx->newObject('modTemplateVar');
    $tv_seo->set('name', 'seo_article');
    $tv_seo->set('caption', 'SEO-статья (HTML)');
    $tv_seo->set('description', 'HTML-контент SEO-статьи под FAQ. Пусто = блок не выводится.');
    $tv_seo->set('type', 'richtext');
    $tv_seo->set('category', $category_id);
    $tv_seo->save();
    echo "[+] TV seo_article создан (id={$tv_seo->get('id')})\n";
} else {
    echo "[=] TV seo_article уже есть (id={$tv_seo->get('id')})\n";
}
$tv_seo_id = $tv_seo->get('id');

// === 3. Привязка TV к шаблонам каталога ===
foreach ([$tv_faq_id, $tv_seo_id] as $tv_id) {
    foreach ($catalog_template_ids as $tpl_id) {
        $exists = $modx->getObject('modTemplateVarTemplate', ['tmplvarid' => $tv_id, 'templateid' => $tpl_id]);
        if (!$exists) {
            $link = $modx->newObject('modTemplateVarTemplate');
            $link->set('tmplvarid', $tv_id);
            $link->set('templateid', $tpl_id);
            $link->save();
        }
    }
}
echo "[+] TV привязаны к шаблонам каталога: " . implode(',', $catalog_template_ids) . "\n";

// === 4. Сниппет renderFaq ===
$snippet = $modx->getObject('modSnippet', ['name' => 'renderFaq']);
$snippet_file = 'core/elements/snippets/renderFaq.php';
if (!$snippet) {
    $snippet = $modx->newObject('modSnippet');
    $snippet->set('name', 'renderFaq');
    $snippet->set('description', 'Рендер FAQ-блока + JSON-LD schema.org/FAQPage из TV faq_data');
    $snippet->set('category', $category_id);
    $snippet->set('static', true);
    $snippet->set('source', 1);
    $snippet->set('static_file', $snippet_file);
    $snippet->save();
    echo "[+] Сниппет renderFaq создан (id={$snippet->get('id')})\n";
} else {
    $snippet->set('static', true);
    $snippet->set('source', 1);
    $snippet->set('static_file', $snippet_file);
    $snippet->save();
    echo "[=] Сниппет renderFaq обновлён (id={$snippet->get('id')})\n";
}

// === 5. Чанки FaqBlock и SeoArticleBlock ===
$chunks_def = [
    'FaqBlock'        => 'core/components/chunks/ObrForm/faqBlock.tpl',
    'SeoArticleBlock' => 'core/components/chunks/ObrForm/seoArticleBlock.tpl',
];
foreach ($chunks_def as $name => $file) {
    $ch = $modx->getObject('modChunk', ['name' => $name]);
    if (!$ch) {
        $ch = $modx->newObject('modChunk');
        $ch->set('name', $name);
        $ch->set('category', $category_id);
        $ch->set('static', true);
        $ch->set('source', 1);
        $ch->set('static_file', $file);
        $ch->save();
        echo "[+] Чанк $name создан (id={$ch->get('id')})\n";
    } else {
        $ch->set('static', true);
        $ch->set('source', 1);
        $ch->set('static_file', $file);
        $ch->save();
        echo "[=] Чанк $name обновлён (id={$ch->get('id')})\n";
    }
}

// === 6. Подключение чанков в шаблоны каталога ===
$db = new PDO('mysql:host=localhost;dbname=obrprofi', 'obrprofi', 'obrprofi');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

foreach ($catalog_template_ids as $tpl_id) {
    $row = $db->query("SELECT templatename, content FROM modx_site_templates WHERE id=$tpl_id")->fetch(PDO::FETCH_ASSOC);
    if (!$row) continue;
    $content = $row['content'];

    // Добавим [[$FaqBlock]] и [[$SeoArticleBlock]] перед [[$catalogFooter?]] (или Footer DPO)
    if (strpos($content, '[[$FaqBlock]]') !== false || strpos($content, '[[$SeoArticleBlock]]') !== false) {
        echo "[=] $tpl_id ({$row['templatename']}) — уже содержит блоки\n";
        continue;
    }

    $insert = "[[\$FaqBlock]]\n\n[[\$SeoArticleBlock]]\n\n";
    $needles = ['[[$catalogFooter?]]', '[[$catalogFooterDPO?]]'];
    $replaced = false;
    foreach ($needles as $needle) {
        if (strpos($content, $needle) !== false) {
            $content = str_replace($needle, $insert . $needle, $content);
            $replaced = true;
            break;
        }
    }
    if ($replaced) {
        $st = $db->prepare("UPDATE modx_site_templates SET content=:c WHERE id=:id");
        $st->execute([':c' => $content, ':id' => $tpl_id]);
        echo "[+] $tpl_id ({$row['templatename']}) — добавлены FaqBlock + SeoArticleBlock\n";
    } else {
        echo "[!] $tpl_id ({$row['templatename']}) — не нашёл точку вставки\n";
    }
}

// === 7. Заполнение TV для пилотной страницы (id=55, Пожарная ПК) ===
$pilot_id = 55;

$faq_json = file_get_contents(__DIR__ . '/faq/faq_pozharnaya_pk_final.json');
$seo_html = file_get_contents(__DIR__ . '/faq/seo_article_pozharnaya_pk.html');

if ($faq_json && $seo_html) {
    $resource = $modx->getObject('modResource', $pilot_id);
    if ($resource) {
        $resource->setTVValue('faq_data', $faq_json);
        $resource->setTVValue('seo_article', $seo_html);
        echo "[+] TV пилотной страницы (id=$pilot_id, Пожарная ПК) заполнены\n";
    } else {
        echo "[!] Ресурс id=$pilot_id не найден\n";
    }
} else {
    echo "[!] Файлы пилотного контента не найдены\n";
}

// === 8. Очистка кеша ===
$modx->cacheManager->refresh();
echo "\n[+] Кеш MODX очищен\n";

echo "\n=== Готово ===\n";
echo "Откройте http://localhost/povyshenie-kvalifikacii/pozharnaya-bezopasnost/ — должен появиться FAQ + SEO-статья.\n";
