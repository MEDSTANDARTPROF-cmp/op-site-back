<?php
/**
 * boxInlineCTA — компактная inline-CTA для встраивания в content блог-статей.
 * Определяет направление по URI/pagetitle (та же логика что boxThemaCoursesTesti)
 * и рендерит ОДНУ кнопку «Узнать о программе» с заголовком про направление.
 *
 * Если направление не определено — return ''.
 *
 * Вызов: [[!boxInlineCTA]]  (вставляется в content статьи)
 */

$uri = $modx->resource ? $modx->resource->get('uri') : '';
$pagetitle = $modx->resource ? mb_strtolower((string)$modx->resource->get('pagetitle')) : '';

$map = [
    [
        'cat' => 49, 'name' => 'Охрана труда', 'short' => 'охране труда',
        'uri' => ['okhrana-truda','oxrana-truda','oxrane-truda','po-oxrane','okhrane-truda','/ot-'],
        'title' => ['охран','охраны труда','спецоценк','соут'],
    ],
    [
        'cat' => 47, 'name' => 'Промышленная безопасность', 'short' => 'промбезопасности',
        'uri' => ['promyshlenn','prombez'],
        'title' => ['промышленн','промбез','опо','ростехнадзор'],
    ],
    [
        'cat' => 58, 'name' => 'Электробезопасность', 'short' => 'электробезопасности',
        'uri' => ['elektrobez','elektro-bez','elektroustanov','elektromont'],
        'title' => ['электробез','группу допуска','группа допуска','электроустановк','электромонт'],
    ],
    [
        'cat' => 55, 'name' => 'Пожарная безопасность', 'short' => 'пожарной безопасности',
        'uri' => ['pozharn','-pb-','/pb-'],
        'title' => ['пожарн','пб ','мчс'],
    ],
    [
        'cat' => 56, 'name' => 'ГО и ЧС', 'short' => 'ГО и ЧС',
        'uri' => ['go-chs','grazhdansk'],
        'title' => ['го и чс','чрезвычайн','гражданск'],
    ],
    [
        'cat' => 52, 'name' => 'Экология', 'short' => 'экологии',
        'uri' => ['ekologiy','ekologi-'],
        'title' => ['эколог','отход'],
    ],
    [
        'cat' => 54, 'name' => 'Теплоэнергетика', 'short' => 'теплоэнергетике',
        'uri' => ['teploenerget','energetichesk'],
        'title' => ['теплоэнерг','энергетик','котел','теплов'],
    ],
    [
        'cat' => 50, 'name' => 'Гостиничное дело', 'short' => 'гостиничному делу',
        'uri' => ['gostinichn','-otel-','/otel'],
        'title' => ['отел','гостиниц','туризм','администратор гостиниц','спир','служба приёма','служба приема'],
    ],
];

$direction = null;
foreach ($map as $cfg) {
    foreach (($cfg['uri'] ?? []) as $key) {
        if ($key !== '' && stripos($uri, $key) !== false) { $direction = $cfg; break 2; }
    }
}
if (!$direction) {
    foreach ($map as $cfg) {
        foreach (($cfg['title'] ?? []) as $key) {
            if ($key !== '' && mb_stripos($pagetitle, $key) !== false) { $direction = $cfg; break 2; }
        }
    }
}

if (!$direction) return '';

$catName  = htmlspecialchars($direction['name'], ENT_QUOTES, 'UTF-8');
$catShort = htmlspecialchars($direction['short'], ENT_QUOTES, 'UTF-8');
$catUrl   = $modx->makeUrl($direction['cat']);
$pageTitleEsc = htmlspecialchars($modx->resource ? $modx->resource->get('pagetitle') : '', ENT_QUOTES, 'UTF-8');

return <<<HTML
<div class="inline-cta my-4 p-4 rounded-3" style="background:linear-gradient(135deg,#f0f7e8 0%,#e8f5e9 100%);border-left:4px solid #06ae1f;">
    <div class="d-flex align-items-center flex-column flex-md-row gap-3">
        <div class="flex-grow-1 text-center text-md-start">
            <div class="fw-7 fs-18 mb-1" style="color:#0a3d62;">Хотите пройти обучение по {$catShort}?</div>
            <div class="small text-secondary">Удостоверение установленного образца с занесением в ФРДО · дистанционно · полное сопровождение</div>
        </div>
        <a href="#" class="btn flex-shrink-0 fw-7 px-4 py-2 d-inline-flex align-items-center gap-2"
           style="background:#06ae1f;color:#fff;border-radius:8px;white-space:nowrap;"
           data-bs-toggle="modal" data-bs-target="#MdGlNew"
           data-form-source="Блог inline CTA ({$catName}): {$pageTitleEsc}"
           data-form-title="Программа по {$catShort}"
           data-form-subtitle="Расскажем о цене, сроках и формате. Менеджер свяжется в течение 15 минут.">
            <span>Узнать о программе</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M13 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>
HTML;
