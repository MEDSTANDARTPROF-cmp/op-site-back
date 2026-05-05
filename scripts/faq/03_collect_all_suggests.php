<?php
/**
 * Этап 3: массовый сбор Yandex Suggest для всех 45 категорий.
 *
 * Для каждой категории:
 *   - формирует базовые запросы из её pagetitle/menutitle
 *   - дополняет вопросительными модификаторами
 *   - парсит Yandex Suggest API
 *   - сохраняет в JSON
 *
 * Время выполнения: ~5-7 минут (тротлинг 150ms между запросами)
 */

$categories = json_decode(file_get_contents(__DIR__ . '/01_categories.json'), true);

function fetchSuggest($q) {
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

$question_words = ['что', 'как', 'кто', 'когда', 'где', 'нужно ли', 'обязательно ли', 'сколько', 'почему', 'через какое', 'для кого', 'какой', 'какая', 'каков', 'можно ли', 'кому', 'нужна ли', 'надо ли', 'обязан', 'кто должен', 'кто может', 'для каких'];

function extractKeywordFromTitle($title) {
    // "Обучение по пожарной безопасности. Курсы повышения квалификации с выдачей удостоверения"
    // → "обучение по пожарной безопасности"
    $title = mb_strtolower($title);
    // Берём всё до первой точки или " — " или " / "
    $title = preg_replace('/[\.—\/].*$/u', '', $title);
    $title = trim($title);
    return $title;
}

$results = [];
$total = 0;
$tStart = microtime(true);

// Выбираем все категории — корни + дочерние
$flat = [];
foreach ($categories as $root) {
    $flat[] = ['id' => $root['id'], 'alias' => $root['alias'], 'title' => $root['pagetitle'], 'menu' => $root['menutitle'], 'uri' => $root['uri'], 'parent' => null];
    foreach ($root['children'] as $c) {
        $flat[] = ['id' => $c['id'], 'alias' => $c['alias'], 'title' => $c['pagetitle'], 'menu' => $c['menutitle'], 'uri' => $c['uri'], 'parent' => $root['alias']];
    }
}

echo "Всего категорий: " . count($flat) . "\n\n";

foreach ($flat as $i => $cat) {
    $kw = extractKeywordFromTitle($cat['title']);
    $menu = mb_strtolower($cat['menu']);

    // 3 базовых запроса по разному
    $bases = array_unique([$kw, $menu, 'курсы ' . $menu, 'обучение ' . $menu]);

    $suggests = [];
    foreach ($bases as $b) {
        if (mb_strlen($b) < 4) continue;
        foreach (fetchSuggest($b) as $s) $suggests[mb_strtolower($s)] = $s;
        usleep(150000);

        // Расширяем вопросительными
        foreach ($question_words as $w) {
            foreach (fetchSuggest($b . ' ' . $w) as $s) $suggests[mb_strtolower($s)] = $s;
            usleep(150000);
        }
    }

    // Фильтр на вопросительные
    $questions = [];
    foreach ($suggests as $low => $orig) {
        foreach ($question_words as $qw) {
            if (mb_stripos($low, $qw) !== false) {
                $questions[] = $orig;
                break;
            }
        }
    }

    $results[] = [
        'id' => $cat['id'],
        'alias' => $cat['alias'],
        'parent' => $cat['parent'],
        'title' => $cat['title'],
        'menu' => $cat['menu'],
        'uri' => $cat['uri'],
        'keyword' => $kw,
        'all_suggestions' => array_values($suggests),
        'question_suggestions' => $questions,
    ];
    $total += count($questions);

    $elapsed = microtime(true) - $tStart;
    echo sprintf("[%2d/%d] %-50s — %d вопросов (%.0fс)\n",
        $i + 1, count($flat), mb_substr($cat['menu'], 0, 50), count($questions), $elapsed);

    // Промежуточное сохранение каждые 10 категорий чтобы не потерять прогресс
    if (($i + 1) % 10 === 0) {
        file_put_contents(__DIR__ . '/03_all_suggestions.json', json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}

file_put_contents(__DIR__ . '/03_all_suggestions.json', json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "\n=== Готово ===\n";
echo "Категорий: " . count($flat) . "\n";
echo "Вопросов всего: " . $total . " (avg " . round($total / count($flat)) . " на категорию)\n";
echo "Время: " . round(microtime(true) - $tStart) . " сек\n";
echo "Сохранено: scripts/faq/03_all_suggestions.json\n";
