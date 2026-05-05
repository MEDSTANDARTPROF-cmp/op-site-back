<?php
/**
 * renderFaq — выводит FAQ-блок и schema.org/FAQPage JSON-LD
 *
 * Читает TV `faq_data` текущего ресурса (формат: {"items": [{"q":"...","a":"..."}]}).
 * Если TV пустой или невалидный JSON — возвращает пустую строку (блок не рендерится).
 *
 * Использование в чанке/шаблоне: [[!renderFaq]]
 */

if (empty($modx->resource)) return '';

$json = $modx->resource->getTVValue('faq_data');
if (empty($json)) return '';

$data = json_decode($json, true);
if (empty($data['items']) || !is_array($data['items'])) return '';

$items = $data['items'];
$title = $modx->getOption('title', $scriptProperties, 'Частые вопросы');

// HTML аккордеон с микроразметкой schema.org
$html  = '<section class="obr-faq" itemscope itemtype="https://schema.org/FAQPage">';
$html .= '<div class="container py-5"><div class="obr-faq__inner">';
$html .= '<h2 class="obr-faq__title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>';
$html .= '<div class="obr-faq__list" id="obrFaqAcc">';

foreach ($items as $i => $item) {
    if (empty($item['q']) || empty($item['a'])) continue;
    $q = htmlspecialchars($item['q'], ENT_QUOTES, 'UTF-8');
    $a = $item['a']; // в ответе может быть HTML — не экранируем
    $aid = 'faq_' . ($i + 1);

    $html .= '<div class="obr-faq__item" itemscope itemtype="https://schema.org/Question" itemprop="mainEntity">';
    $html .= '<h3 class="obr-faq__q-wrap">';
    $html .= '<button type="button" class="obr-faq__q collapsed" data-bs-toggle="collapse" data-bs-target="#' . $aid . '" aria-expanded="false" aria-controls="' . $aid . '">';
    $html .= '<span itemprop="name">' . $q . '</span>';
    $html .= '<span class="obr-faq__chevron" aria-hidden="true"></span>';
    $html .= '</button>';
    $html .= '</h3>';
    $html .= '<div id="' . $aid . '" class="collapse obr-faq__a-wrap" itemscope itemtype="https://schema.org/Answer" itemprop="acceptedAnswer">';
    $html .= '<div class="obr-faq__a" itemprop="text">' . $a . '</div>';
    $html .= '</div>';
    $html .= '</div>';
}

$html .= '</div></div></div></section>';

// JSON-LD для богатых сниппетов в Яндексе/Google
$ld = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => [],
];
foreach ($items as $item) {
    if (empty($item['q']) || empty($item['a'])) continue;
    $ld['mainEntity'][] = [
        '@type'          => 'Question',
        'name'           => $item['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => strip_tags($item['a']),
        ],
    ];
}
$ld_json = json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$html .= "\n" . '<script type="application/ld+json">' . $ld_json . '</script>' . "\n";

return $html;