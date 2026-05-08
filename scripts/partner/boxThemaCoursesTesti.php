<?php
/**
 * boxThemaCoursesTesti — рендер блока «Тематические программы» для страниц /testi/.
 *
 * Определяет направление по URI текущего ресурса и подтягивает 3 программы из
 * соответствующей категории. Если URI не маппится — возвращает пустую строку.
 *
 * Вызов: [[!boxThemaCoursesTesti]]
 */

$uri = $modx->resource ? $modx->resource->get('uri') : '';

// Маппинг направления → category id (родитель курсов)
$map = [
    'promyshlennaia-bezopasnost' => ['cat' => 47, 'name' => 'Промышленная безопасность', 'short' => 'промбезопасности'],
    'elektrobezopasnost'         => ['cat' => 58, 'name' => 'Электробезопасность', 'short' => 'электробезопасности'],
    'energeticheskaia-bezopasnost'=> ['cat' => 54, 'name' => 'Теплоэнергетика', 'short' => 'теплоэнергетике'],
    'okhrana-truda'              => ['cat' => 49, 'name' => 'Охрана труда', 'short' => 'охране труда'],
];

$direction = null;
foreach ($map as $key => $cfg) {
    if (strpos($uri, $key) !== false) { $direction = $cfg; break; }
}

if (!$direction) return '';

// Получаем 3 опубликованных курса из категории
$q = $modx->newQuery('modResource', [
    'parent'    => $direction['cat'],
    'deleted'   => 0,
    'published' => 1,
    'isfolder'  => 0,
]);
$q->limit(3);
$q->sortby('menuindex', 'ASC');
$courses = $modx->getCollection('modResource', $q);

if (count($courses) === 0) return '';

// URL категории
$catUrl = $modx->makeUrl($direction['cat']);

// HTML блока
$cards = '';
foreach ($courses as $c) {
    $title = htmlspecialchars($c->get('pagetitle'), ENT_QUOTES, 'UTF-8');
    $url = $modx->makeUrl($c->get('id'));
    $price = $c->getTVValue('price');
    if ($price) {
        $price = preg_replace('/[^0-9]/', '', $price);
        $price = $price ? (number_format($price, 0, '.', ' ').' ₽') : '';
    }
    $img = $c->getTVValue('offerImg') ?: $c->getTVValue('offerImgBg') ?: 'assets/img/categories/cat-'.$direction['cat'].'.png';
    $thumb = $modx->runSnippet('phpthumbon', ['input' => $img, 'options' => '&w=320&h=200&zc=1&far=BC&f=webp&q=82']);
    $priceHtml = $price ? "<div class=\"thema-card__price\">от <b>$price</b></div>" : '';

    $cards .= <<<HTML
<a href="$url" class="thema-card">
    <div class="thema-card__img" style="background-image: url('$thumb');"></div>
    <div class="thema-card__body">
        <div class="thema-card__title">$title</div>
        $priceHtml
    </div>
</a>
HTML;
}

$catName  = $direction['name'];
$catShort = $direction['short'];

return <<<HTML
<style>
.thema-block { background: #fff; border-radius: 12px; padding: 22px 24px; margin-top: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.thema-block__head { margin-bottom: 16px; }
.thema-block__eyebrow { font-size: 11px; font-weight: 700; letter-spacing: 1.5px; color: #06ae1f; text-transform: uppercase; margin-bottom: 6px; }
.thema-block__title { font-size: 18px; font-weight: 700; color: #0a3d62; line-height: 1.3; }
.thema-block__sub { font-size: 13px; color: #555; margin-top: 4px; }
.thema-block__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 16px; }
@media (max-width: 768px) {
    .thema-block__grid { grid-template-columns: 1fr; }
}
.thema-card {
    display: flex; flex-direction: column;
    border: 1px solid #e3e8ee; border-radius: 10px;
    overflow: hidden; text-decoration: none; color: inherit;
    transition: transform .15s, box-shadow .15s, border-color .15s;
}
.thema-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(6,174,31,0.15);
    border-color: #06ae1f;
    text-decoration: none;
    color: inherit;
}
.thema-card__img { width: 100%; aspect-ratio: 16/10; background-size: cover; background-position: center; background-color: #e8f5e9; }
.thema-card__body { padding: 12px 14px; flex: 1; display: flex; flex-direction: column; gap: 6px; }
.thema-card__title { font-size: 13px; font-weight: 600; line-height: 1.35; color: #0a3d62; }
.thema-card__price { font-size: 13px; color: #555; margin-top: auto; }
.thema-card__price b { color: #06ae1f; font-weight: 700; font-size: 15px; }
.thema-block__cta { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-top: 14px; border-top: 1px solid #f0f3f6; }
.thema-block__cta-text { font-size: 13px; color: #555; }
.thema-block__cta-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 16px; background: #06ae1f; color: #fff;
    border-radius: 6px; font-weight: 600; font-size: 13px;
    text-decoration: none; transition: background .15s;
}
.thema-block__cta-btn:hover { background: #058518; color: #fff; text-decoration: none; }
@media (max-width: 768px) {
    .thema-block__cta { flex-direction: column; align-items: stretch; text-align: center; }
    .thema-block__cta-btn { justify-content: center; }
}
</style>

<div class="thema-block">
    <div class="thema-block__head">
        <div class="thema-block__eyebrow">Готовитесь к аттестации?</div>
        <div class="thema-block__title">Программы обучения по {$catShort}</div>
        <div class="thema-block__sub">Удостоверение установленного образца с занесением в ФРДО</div>
    </div>
    <div class="thema-block__grid">$cards</div>
    <div class="thema-block__cta">
        <span class="thema-block__cta-text">Все программы по направлению «$catName»</span>
        <a href="$catUrl" class="thema-block__cta-btn">Смотреть каталог →</a>
    </div>
</div>
HTML;
