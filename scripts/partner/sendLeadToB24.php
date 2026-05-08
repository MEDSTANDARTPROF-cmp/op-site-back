/**
 * sendLeadToB24 — приём AJAX-заявки с фронта и проброс в Битрикс24.
 *
 * Вызывается из MODX-ресурса (alias: ajax-lead) с типом ответа JSON.
 * Принимает POST с полями:
 *   name, phone, email (опц.), message (опц.),
 *   channel (phone|email|wa|tg|viber|max),
 *   form_source (контекст — карточка/категория/B2B и т.п.),
 *   page_title, page_url
 *
 * Возвращает JSON:
 *   {ok: true, lead_id: 123} — успешно создан в B24
 *   {ok: true, test_mode: true} — локальный тест, в B24 не отправлено
 *   {ok: false, error: "..."} — ошибка
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    return;
}

$config_path = MODX_CORE_PATH . 'config/b24.config.php';
if (!file_exists($config_path)) {
    echo json_encode(['ok' => false, 'error' => 'config_missing']);
    return;
}
$config = require $config_path;

// === Безопасность: проверка Origin/Referer ===
// Запрос должен приходить с нашего сайта, не с произвольных источников.
$allowed_hosts = array_merge(
    $config['test_hosts'] ?? [],
    ['obrprofi.ru', 'dev.obrprofi.ru']
);
$referer_host = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST) ?: '';
$origin_host  = parse_url($_SERVER['HTTP_ORIGIN'] ?? '', PHP_URL_HOST) ?: '';
$check_host   = $referer_host ?: $origin_host;
$origin_ok    = false;
foreach ($allowed_hosts as $h) {
    if ($check_host !== '' && stripos($check_host, $h) !== false) {
        $origin_ok = true;
        break;
    }
}
if (!$origin_ok) {
    echo json_encode(['ok' => false, 'error' => 'invalid_origin']);
    return;
}

// === Honeypot ===
$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') {
    echo json_encode(['ok' => true, 'lead_id' => 0]);
    return;
}

// === Проверка времени заполнения (бот шлёт мгновенно) ===
$form_open_at = (int)($_POST['form_open_at'] ?? 0);
if ($form_open_at > 0) {
    $now_ms = (int)(microtime(true) * 1000);
    $elapsed_ms = $now_ms - $form_open_at;
    if ($elapsed_ms < 1500) {
        // Меньше 1.5 секунд — точно бот. Тихо игнорируем.
        echo json_encode(['ok' => false, 'error' => 'too_fast']);
        return;
    }
}

// === Чтение и обрезка полей до разумных лимитов ===
$name        = mb_substr(trim($_POST['name']        ?? ''), 0, 100);
$phone       = mb_substr(trim($_POST['phone']       ?? ''), 0, 30);
$email       = mb_substr(trim($_POST['email']       ?? ''), 0, 100);
$message     = mb_substr(trim($_POST['message']     ?? ''), 0, 2000);
$channel     = mb_substr(trim($_POST['channel']     ?? 'phone'), 0, 20);
$form_source = mb_substr(trim($_POST['form_source'] ?? 'Общий CTA'), 0, 500);
$page_title  = mb_substr(trim($_POST['page_title']  ?? ''), 0, 500);
$page_url    = mb_substr(trim($_POST['page_url']    ?? ''), 0, 1000);

// B2B-поля (приходят с boxB2BForm)
$is_b2b      = (string)($_POST['b2b'] ?? '') === '1';
$inn         = preg_replace('/\D+/', '', (string)($_POST['inn'] ?? ''));
$employees   = mb_substr(trim($_POST['employees'] ?? ''), 0, 30);
if ($is_b2b) {
    if (strlen($inn) !== 10 && strlen($inn) !== 12) {
        echo json_encode(['ok' => false, 'error' => 'invalid_inn']);
        return;
    }
    if ($employees === '') {
        echo json_encode(['ok' => false, 'error' => 'employees_required']);
        return;
    }
}

$allowed_channels = ['phone', 'email', 'wa', 'tg', 'viber', 'max'];
if (!in_array($channel, $allowed_channels, true)) {
    $channel = 'phone';
}

$phone_digits = preg_replace('/\D+/', '', $phone);
if ($channel === 'email') {
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok' => false, 'error' => 'invalid_email']);
        return;
    }
} else {
    if (strlen($phone_digits) < 10) {
        echo json_encode(['ok' => false, 'error' => 'invalid_phone']);
        return;
    }
}

if (function_exists('apcu_fetch')) {
    $rate_key = 'b24_lead_' . md5(($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $phone_digits);
    if (apcu_fetch($rate_key)) {
        echo json_encode(['ok' => false, 'error' => 'rate_limit']);
        return;
    }
    apcu_store($rate_key, 1, (int)$config['rate_limit_seconds']);
}

$channel_labels = [
    'phone' => 'Звонок',
    'email' => 'Email',
    'wa'    => 'WhatsApp',
    'tg'    => 'Telegram',
    'viber' => 'Viber',
    'max'   => 'MAX',
];
$channel_label = $channel_labels[$channel];

$current_host = $_SERVER['HTTP_HOST'] ?? '';
$is_test_host = false;
foreach ($config['test_hosts'] as $test_host) {
    if (stripos($current_host, $test_host) !== false) {
        $is_test_host = true;
        break;
    }
}

$title_prefix = $is_test_host ? '[ТЕСТ-LOCAL] ' : '';
if ($is_b2b) {
    $title = $title_prefix . 'B2B-заявка ОБРПРОФИ — ИНН ' . $inn . ' (' . $employees . ' чел.)';
} else {
    $title = $title_prefix . 'Заявка с сайта ОБРПРОФИ (' . $channel_label . ')';
}

$comments_lines = [];
if ($is_b2b) {
    $comments_lines[] = '=== B2B-ЗАЯВКА ===';
    $comments_lines[] = 'ИНН компании: ' . $inn;
    $comments_lines[] = 'Сотрудников на обучение: ' . $employees;
    $comments_lines[] = '';
}
if ($form_source !== '') {
    $comments_lines[] = 'Источник на сайте: ' . $form_source;
}
if ($page_title !== '') {
    $comments_lines[] = 'Страница: ' . $page_title;
}
if ($page_url !== '') {
    $comments_lines[] = 'URL: ' . $page_url;
}
$comments_lines[] = 'Удобный канал связи: ' . $channel_label;
if ($message !== '') {
    $comments_lines[] = '';
    $comments_lines[] = 'Сообщение клиента:';
    $comments_lines[] = $message;
}
$comments = implode("\n", $comments_lines);

$source_description = 'Предпочитаемый канал связи: ' . $channel_label;

// Тип устройства из User-Agent (как у medstandartprof: desktop / mobile / tablet)
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$device_type = 'desktop';
if (preg_match('/(iPad|Tablet|PlayBook)/i', $ua) || (preg_match('/Android/i', $ua) && !preg_match('/Mobile/i', $ua))) {
    $device_type = 'tablet';
} elseif (preg_match('/(Mobile|iPhone|iPod|Android|BlackBerry|Opera Mini|IEMobile)/i', $ua)) {
    $device_type = 'mobile';
}

$fields = [
    'TITLE'              => $title,
    'NAME'               => $name !== '' ? $name : 'Без имени',
    'COMMENTS'           => $comments,
    'SOURCE_ID'          => 'RC_GENERATOR', // справочник B24: "Заявка с сайта obrprofi.ru"
    'SOURCE_DESCRIPTION' => $source_description,

    // Пользовательские поля (UF) — структурированно, как у medstandartprof.ru.
    // Поля общие на портале B24, ничего нового создавать не надо.
    'UF_CRM_LEAD_1737612814914' => $page_url,        // URL страницы
    'UF_CRM_1738819361031'      => $page_title,      // Страница, откуда пришла заявка
    'UF_CRM_1739333675514'      => $form_source,     // Кнопка, через которую заполнили форму
    'UF_CRM_1740631494137'      => $device_type,     // Тип устройства (desktop/mobile/tablet)
];
if ($phone !== '') {
    $fields['PHONE'] = [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];
}
if ($email !== '') {
    $fields['EMAIL'] = [['VALUE' => $email, 'VALUE_TYPE' => 'WORK']];
}

$log_dir = MODX_CORE_PATH . 'cache/logs/';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}
$log_path = $log_dir . $config['log_file'];
$log_entry = '[' . date('Y-m-d H:i:s') . '] '
    . ($is_test_host ? '[TEST] ' : '[LIVE] ')
    . 'host=' . $current_host . ' channel=' . $channel . ' phone=' . $phone . ' email=' . $email
    . "\n" . $title . "\n" . $comments . "\n---\n";
@file_put_contents($log_path, $log_entry, FILE_APPEND | LOCK_EX);

$webhook = rtrim($config['webhook_url'], '/');
$endpoint = $webhook . '/crm.lead.add.json';

// B24 REST лучше всего работает с application/x-www-form-urlencoded
// через http_build_query (вложенные массивы fields[KEY], PHONE[0][VALUE] и т.п.).
// Когда отправляли JSON — B24 принимал, но поля не разбирались.
$payload = http_build_query(['fields' => $fields, 'params' => ['REGISTER_SONET_EVENT' => 'Y']]);

$ch = curl_init($endpoint);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false || $http_code !== 200) {
    @file_put_contents($log_path,
        '[' . date('Y-m-d H:i:s') . '] [B24-ERROR] http=' . $http_code . ' err=' . $curl_error . ' resp=' . substr((string)$response, 0, 500) . "\n---\n",
        FILE_APPEND | LOCK_EX);
    echo json_encode(['ok' => false, 'error' => 'b24_unavailable']);
    return;
}

$data = json_decode($response, true);
if (!is_array($data) || isset($data['error'])) {
    @file_put_contents($log_path,
        '[' . date('Y-m-d H:i:s') . '] [B24-ERROR] resp=' . substr($response, 0, 500) . "\n---\n",
        FILE_APPEND | LOCK_EX);
    echo json_encode(['ok' => false, 'error' => 'b24_rejected']);
    return;
}

$lead_id = (int)($data['result'] ?? 0);
@file_put_contents($log_path,
    '[' . date('Y-m-d H:i:s') . '] [B24-OK] lead_id=' . $lead_id . "\n---\n",
    FILE_APPEND | LOCK_EX);

echo json_encode(['ok' => true, 'lead_id' => $lead_id]);