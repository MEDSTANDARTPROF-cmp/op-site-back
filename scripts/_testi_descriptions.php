<?php
/**
 * Generate and apply meta descriptions for all /testi/ pages.
 *
 * Strategy:
 *  - depth 4 (tickets, 1998): "Билет №X онлайн-тренажёр / с готовыми ответами по теме «{area}». N вопросов с разбором…"
 *  - depth 3 (test groups, 168): "{full pagetitle} — N билетов, онлайн-тренажёр…"
 *  - depth 2 (8): bespoke per page based on direction
 *  - depth 0-1 (4): manual map
 *
 * Modes:
 *   --dry      print first 10 of each bucket, don't write
 *   --apply    write to DB
 *   --backup   write current descriptions to CSV first (recommended before --apply)
 */
define('MODX_API_MODE', true);
$idx = dirname(__DIR__).'/site/index.php';
if (!file_exists($idx)) $idx = dirname(__DIR__).'/index.php';
require_once $idx;

$args = $argv;
$DRY     = in_array('--dry', $args);
$APPLY   = in_array('--apply', $args);
$BACKUP  = in_array('--backup', $args);
if (!$DRY && !$APPLY) { echo "Usage: php _testi_descriptions.php --dry | --apply [--backup]\n"; exit(1); }

function uri_depth($u){ return substr_count(rtrim($u,'/'),'/'); }
function clean($s){ return trim(preg_replace('/\s+/u',' ', $s)); }

function shorten_title($t){
    // Drop "(октябрь 2024)" trailing
    $t = preg_replace('/\s*\(?[а-я]+\s+\d{4}\)?\s*$/u', '', $t);
    return trim($t);
}

function gen_for_ticket($r, $modx) {
    $uri = $r->get('uri');
    $isTrenajer = strpos($uri, 'testi/onlajn-trenazher/') === 0;
    $title = $r->get('pagetitle'); // "Билет №X"

    $parent = $modx->getObject('modResource', $r->get('parent'));
    $parentTitle = $parent ? $parent->get('pagetitle') : '';
    if (getenv('DEBUG')) {
        echo "DEBUG ticket ".$r->get('id')." parent_id=".$r->get('parent')." parentTitle='$parentTitle' parentObj=".($parent ? 'YES' : 'NO')."\n";
    }
    $area = shorten_title($parentTitle);
    if ($area === '') $area = $parentTitle; // fallback if shortener wiped it
    if (getenv('DEBUG2')) echo "AREA='$area' (len ".mb_strlen($area).")\n";

    $qsTV = $r->getTVValue('testQuestions');
    $qcount = $qsTV ? count(array_filter(explode(',', $qsTV), 'strlen')) : 10;

    if ($isTrenajer) {
        return clean($title.' — пройти онлайн-тест по теме «'.$area.'». '.$qcount.' вопросов с разбором ответов, мгновенный результат, без регистрации. Подготовка к аттестации в Ростехнадзоре.');
    } else {
        return clean($title.' с готовыми ответами по теме «'.$area.'». Полный текст билета, '.$qcount.' вопросов и правильные ответы для подготовки к аттестации в Ростехнадзоре.');
    }
}

function gen_for_group($r, $modx) {
    $uri = $r->get('uri');
    $isTrenajer = strpos($uri, 'testi/onlajn-trenazher/') === 0;
    $title = clean($r->get('pagetitle'));

    if ($isTrenajer) {
        // d3 в тренажёре = группа билетов
        $cnt = $modx->getCount('modResource', ['parent'=>$r->get('id'), 'deleted'=>0, 'published'=>1]);
        return clean($title.' — '.$cnt.' билетов, онлайн-тренажёр с разбором ответов и мгновенным результатом. Подготовка к аттестации в Ростехнадзоре без регистрации.');
    } else {
        // d3 в ответах = листовая страница со сводкой ответов
        return clean('Готовые ответы по теме «'.$title.'» — полные тексты билетов и правильные ответы с пояснениями для подготовки к аттестации в Ростехнадзоре.');
    }
}

// depth 2 — 8 sections — manual map
$DEPTH2_MAP = [
    'testi/onlajn-trenazher/promyshlennaia-bezopasnost-testy-rostekhnadzora/' =>
        'Промышленная безопасность — онлайн-тренажёр по тестам Ростехнадзора (А.1, Б.1–Б.12). Пройдите билеты с разбором ответов и подготовьтесь к аттестации без регистрации.',
    'testi/onlajn-trenazher/elektrobezopasnost/' =>
        'Электробезопасность — онлайн-тренажёр по тестам Ростехнадзора (II–V группы допуска до и выше 1000 В). Билеты с разбором ответов для подготовки к аттестации.',
    'testi/onlajn-trenazher/energeticheskaia-bezopasnost/' =>
        'Энергетическая безопасность — онлайн-тренажёр по тестам Ростехнадзора (Г.1, Г.2, область STEP). Билеты с ответами и разбором для подготовки к аттестации.',
    'testi/onlajn-trenazher/okhrana-truda/' =>
        'Охрана труда — онлайн-тренажёр для проверки знаний по охране труда в организациях и офисах. Тесты с разбором ответов в формате аттестационной комиссии.',
    'testi/otvetyi/promyshlennaia-bezopasnost-testy-rostekhnadzora/' =>
        'Промышленная безопасность — готовые ответы на билеты Ростехнадзора (А.1, Б.1–Б.12) с пояснениями. Подготовка к аттестации без регистрации.',
    'testi/otvetyi/elektrobezopasnost/' =>
        'Электробезопасность — готовые ответы на билеты Ростехнадзора по II–V группам допуска. Полный список вопросов с правильными ответами и пояснениями.',
    'testi/otvetyi/energeticheskaia-bezopasnost/' =>
        'Энергетическая безопасность — готовые ответы на билеты Ростехнадзора (Г.1, Г.2, область STEP) с пояснениями для подготовки к аттестации.',
    'testi/otvetyi/okhrana-truda/' =>
        'Охрана труда — готовые ответы на тесты по охране труда с пояснениями. Подготовка к проверке знаний в формате аттестационной комиссии.',
];

// depth 0-1 — 4 pages
$DEPTH01_MAP = [
    'testi/' => 'Тесты Ростехнадзора и охраны труда онлайн — тренажёры и готовые ответы для подготовки к аттестации. Промбезопасность, электробезопасность, энергетика, охрана труда.',
    'testi/onlajn-trenazher/' => 'Онлайн-тренажёр по тестам Ростехнадзора — промышленная безопасность, электробезопасность, энергетика, охрана труда. Билеты с разбором ответов без регистрации.',
    'testi/otvetyi/' => 'Готовые ответы на тесты Ростехнадзора и охраны труда — полные билеты с пояснениями. Промбезопасность, электробезопасность, энергетика, охрана труда.',
    'testi/rezultat-poiska-testyi.html' => 'Результаты поиска по тестам Ростехнадзора и охраны труда. Подберите нужный билет для онлайн-тренировки или готовых ответов.',
];

// --- Run ---
$q = $modx->newQuery('modResource', ['deleted'=>0, 'published'=>1]);
$q->where(['uri:LIKE'=>'testi/%']);
$rows = $modx->getCollection('modResource', $q);

$updated = 0;
$bucket_samples = ['d4_train'=>[], 'd4_otv'=>[], 'd3_train'=>[], 'd3_otv'=>[], 'd2'=>[], 'd01'=>[]];
$applied = [];

if ($BACKUP) {
    $bk = dirname(__DIR__).'/scripts/_testi_descriptions_backup.csv';
    $fp = fopen($bk, 'w');
    fputcsv($fp, ['id','uri','old_description']);
    foreach ($rows as $r) fputcsv($fp, [$r->get('id'), $r->get('uri'), $r->get('description')]);
    fclose($fp);
    echo "Backup saved: $bk (".count($rows)." rows)\n\n";
}

foreach ($rows as $r) {
    $uri = $r->get('uri');
    $depth = uri_depth($uri);
    $newDesc = null;
    $bucket = '';

    if ($depth >= 4) {
        $newDesc = gen_for_ticket($r, $modx);
        $bucket = (strpos($uri, 'onlajn-trenazher') !== false) ? 'd4_train' : 'd4_otv';
    } elseif ($depth == 3) {
        $newDesc = gen_for_group($r, $modx);
        $bucket = (strpos($uri, 'onlajn-trenazher') !== false) ? 'd3_train' : 'd3_otv';
    } elseif ($depth == 2) {
        $newDesc = $DEPTH2_MAP[$uri] ?? null;
        $bucket = 'd2';
    } elseif ($depth <= 1) {
        $newDesc = $DEPTH01_MAP[$uri] ?? null;
        $bucket = 'd01';
    }

    if (!$newDesc) continue;

    if (count($bucket_samples[$bucket]) < 3) {
        $bucket_samples[$bucket][] = ['uri'=>$uri,'len'=>mb_strlen($newDesc),'text'=>$newDesc];
    }

    if ($APPLY) {
        $r->set('description', $newDesc);
        $r->save();
        $updated++;
    }
}

if ($DRY) {
    foreach ($bucket_samples as $bk => $samples) {
        echo "=== $bk ===\n";
        foreach ($samples as $s) {
            echo "  [".$s['len']."] ".$s['uri']."\n    ".$s['text']."\n";
        }
        echo "\n";
    }
    echo "DRY mode — nothing written. Total candidates: ".count($rows)."\n";
} else {
    echo "Updated: $updated rows\n";
    $modx->cacheManager->refresh();
    echo "Cache refreshed.\n";
}
