<?php
/**
 * Этап 2 (пилот): собираем подсказки Yandex для одной категории.
 * Показываем что получается, чтобы оценить качество источника.
 */

// Базовые формулировки запроса для категории «Обучение по пожарной безопасности» (ПК)
$base_queries = [
    'обучение по пожарной безопасности',
    'курсы по пожарной безопасности',
    'повышение квалификации по пожарной безопасности',
    'пожарно-технический минимум',
    'пожарная безопасность',
];

function fetchYandexSuggest($q) {
    $url = 'https://suggest.yandex.ru/suggest-ya.cgi?'
        . http_build_query([
            'part'   => $q,
            'v'      => 4,
            'srv'    => 'morda_ru_desk',
            'uil'    => 'ru',
            'fact'   => 1,
            'pers'   => 0,
        ]);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0',
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

    // Yandex suggest возвращает: ["query","suggestions","factual"...]
    // suggestions могут быть в виде [["text","..."], ...] или просто [str, str]
    $arr = json_decode($resp, true);
    if (!is_array($arr) || count($arr) < 2) return [];
    $suggestions = is_array($arr[1]) ? $arr[1] : [];
    $clean = [];
    foreach ($suggestions as $s) {
        if (is_array($s)) $s = $s[0] ?? '';
        if (is_string($s) && $s !== '') $clean[] = $s;
    }
    return $clean;
}

// Алфавит-расширение: к каждому базовому запросу добавляем "а", "б", "в"... — Yandex даст разные подсказки
$letters_for_questions = ['что', 'как', 'кто', 'когда', 'где', 'нужно', 'обязательно', 'сколько', 'почему', 'через', 'для кого', 'какой', 'можно ли'];

$all_suggestions = [];
foreach ($base_queries as $q) {
    $sg = fetchYandexSuggest($q);
    foreach ($sg as $s) $all_suggestions[mb_strtolower($s)] = $s;

    // По «расширенным» вопросам
    foreach ($letters_for_questions as $w) {
        $extQ = $q . ' ' . $w;
        $sg2 = fetchYandexSuggest($extQ);
        foreach ($sg2 as $s) $all_suggestions[mb_strtolower($s)] = $s;
        usleep(150000); // 150ms между запросами чтобы не банили
    }
}

echo "Всего собрано уникальных подсказок: " . count($all_suggestions) . "\n\n";

// Фильтр: только подсказки которые звучат как вопросы
// (содержат вопросительные слова или начинаются с глагола)
$question_words = ['что', 'как', 'кто', 'когда', 'где', 'нужно', 'обязательно', 'сколько', 'почему', 'через', 'для кого', 'какой', 'какая', 'каков', 'можно ли', 'кому', 'нужна ли', 'сколько стоит', 'сколько часов', 'кто проходит', 'надо ли', 'обязан', 'обязана', 'кто должен', 'кто может'];

$questions = [];
$other = [];
foreach ($all_suggestions as $low => $orig) {
    $matched = false;
    foreach ($question_words as $qw) {
        if (mb_stripos($low, $qw) !== false) {
            $questions[] = $orig;
            $matched = true;
            break;
        }
    }
    if (!$matched) $other[] = $orig;
}

echo "=== Вопросительные подсказки (" . count($questions) . " шт.) ===\n";
foreach ($questions as $q) echo "  ? $q\n";

echo "\n=== Прочие подсказки (топ-30) ===\n";
foreach (array_slice($other, 0, 30) as $q) echo "  - $q\n";

// Сохраняем
file_put_contents(__DIR__ . '/02_pilot_suggestions.json', json_encode([
    'category' => 'Пожарная безопасность (ПК)',
    'base_queries' => $base_queries,
    'questions' => $questions,
    'other' => $other,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "\nСохранено: scripts/faq/02_pilot_suggestions.json\n";
