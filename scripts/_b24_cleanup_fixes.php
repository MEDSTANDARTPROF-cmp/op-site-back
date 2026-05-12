<?php
/**
 * Post-cleanup fixes — точечные правки по результатам визуального ревью.
 *  1. msProduct.content.prod: убрать пустой wrapper от B24 + вставить [[$ObrFormFindCourse]]
 *  2. /blog/udalit.html (id=4714): удалить (тестовая страница в черновиках)
 *  3. Template 8 «Отзывы»: добавить отступ для h1 + хлебных крошек
 *  4. boxPrepod.Tpl: WhatsApp-кнопку → наш модал #MdGlNew
 */
define('MODX_API_MODE', true);
require_once dirname(__DIR__).'/site/index.php';

// === 1. msProduct.content.prod — пустой wrapper заменить на ObrFormFindCourse ===
$c = $modx->getObject('modChunk', ['name'=>'msProduct.content.prod']);
$b = $c->get('snippet');
$pattern = '/<div class="bg-white rounded-4 shadow overflow-hidden mt-5">\s*<div class="bg-white p-5 p-6[^"]*"[^>]*>\s*<\/div>\s*<\/div>/s';
$replacement = '[[$ObrFormFindCourse]]';
$new = preg_replace($pattern, $replacement, $b, 1, $cnt);
if ($cnt) {
    $c->set('snippet', $new);
    $c->save();
    echo "[1] msProduct.content.prod: empty wrapper -> [[\$ObrFormFindCourse]] (delta ".(strlen($new)-strlen($b)).")\n";
} else {
    echo "[1] msProduct.content.prod: pattern not found\n";
}

// === 2. Удалить /blog/udalit.html ===
$r = $modx->getObject('modResource', 4714);
if ($r && $r->get('uri') === 'blog/udalit.html') {
    $r->set('deleted', 1);
    $r->set('deletedon', time());
    $r->set('deletedby', 1);
    $r->save();
    echo "[2] /blog/udalit.html (id=4714) marked deleted\n";
} else {
    echo "[2] resource 4714 not found or different URI\n";
}

// === 3. Template 8 — добавить отступы для h1 + breadcrumbs ===
$t = $modx->getObject('modTemplate', 8);
$b = $t->get('content');
// hero h1 контейнер — добавим px-md-3
$new = str_replace(
    '<div class="col-12 col-md-6 mb-5">',
    '<div class="col-12 col-md-6 mb-5 px-md-3">',
    $b
);
// breadcrumbs контейнер
$new = str_replace(
    '<div class="container my-3 pb-2 px-4 px-md-0" >',
    '<div class="container my-3 pb-2 px-4 px-md-3" >',
    $new
);
// h1 row also
$new = str_replace(
    '<div class="container mb-5 px-4 px-md-0">',
    '<div class="container mb-5 px-4 px-md-3">',
    $new
);
if ($new !== $b) {
    $t->set('content', $new);
    $t->save();
    echo "[3] template 8 «Отзывы»: добавлены отступы (px-md-3)\n";
} else {
    echo "[3] template 8: no change applied (paddings may already be set)\n";
}

// === 4. boxPrepod.Tpl — WhatsApp кнопку на наш модал ===
$c = $modx->getObject('modChunk', ['name'=>'boxPrepod.Tpl']);
$b = $c->get('snippet');
$wa_block = <<<'HTML'
<a onclick="ym(75081295,'reachGoal','WA')" href="https://wa.me/79292101126" class="btn btn-lim btn-ic py-2 pe-4 24-form-marker" target="_blank" rel="noopener noreferrer">
                    <img src="assets/icon/coll.svg" width="32" height="32" alt="Оставить заявку">
                    <span>Перейти в чат*</span></a>
HTML;
$new_block = <<<'HTML'
<a href="#" class="btn btn-lim btn-ic py-2 pe-4" data-bs-toggle="modal" data-bs-target="#MdGlNew" data-form-source="Тимур-блок «Остались вопросы»: [[*pagetitle]]" data-form-title="Консультация" data-form-subtitle="Менеджер свяжется с вами в течение 15 минут и ответит на все вопросы по программе.">
                    <img src="assets/icon/coll.svg" width="32" height="32" alt="Оставить заявку">
                    <span>Оставить заявку</span></a>
HTML;
$new = str_replace($wa_block, $new_block, $b);
// Также поправить текст параграфа: «можете написать мне в WhatsApp» → «можете оставить заявку»
$new = str_replace(
    'Для получения консультации вы можете написать мне в WhatsApp:',
    'Для получения консультации вы можете оставить заявку — менеджер свяжется в течение 15 минут:',
    $new
);
if ($new !== $b) {
    $c->set('snippet', $new);
    $c->save();
    echo "[4] boxPrepod.Tpl: WhatsApp-кнопка → #MdGlNew (delta ".(strlen($new)-strlen($b)).")\n";
} else {
    echo "[4] boxPrepod.Tpl: pattern not found\n";
}

$modx->cacheManager->refresh();
echo "\nCache refreshed.\n";
