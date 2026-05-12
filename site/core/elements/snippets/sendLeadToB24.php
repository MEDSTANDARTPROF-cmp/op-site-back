<?php
/**
 * sendLeadToB24 — приём AJAX-заявки с фронта и проброс в Битрикс24.
 *
 * Вызывается из MODX-ресурса (alias: ajax-lead) с типом ответа JSON.
 * Принимает POST с полями:
 *   name, phone, email (опц.), message (опц.),
 *   channel (phone|email|wa|tg|viber|max),
 *   form_source (контекст — карточка/категория/B2B и т.п.),
 *   page_title, page_url,
 *   b2b, inn, employees (для B2B-формы)
 *
 * Возвращает JSON:
 *   {ok: true, lead_id: 123} — успешно создан в B24
 *   {ok: true, test_mode: true} — локальный тест, в B24 не отправлено
 *   {ok: false, error: "..."} — ошибка/отказ
 *
 * Логирование (core/cache/logs/b24_leads.log):
 *   [LIVE] / [TEST]  — попытка принята, отправлена в B24
 *   [B24-OK]         — B24 принял, lead_id=N
 *   [B24-ERROR]      — B24 ответил ошибкой или curl упал
 *   [REJECT]         — отказ на валидации (origin/phone/email/rate)
 *   [BOT-REJECT]     — отбит как бот (honeypot/too_fast) — silent
 *
 * Telegram-уведомления — по конфигу telegram_bot_token + telegram_chat_id в b24.config.php.
 * Отправляются для всех событий КРОМЕ [BOT-REJECT].
 */

header('Content-Type: application/json; charset=utf-8');

// === Конфиг и инициализация лога ===
$config_path = MODX_CORE_PATH . 'config/b24.config.php';
if (!file_exists($config_path)) {
    echo json_encode(['ok' => false, 'error' => 'config_missing']);
    return;
}
$config = require $config_path;

$log_dir = MODX_CORE_PATH . 'cache/logs/';
if (!is_dir($log_dir)) @mkdir($log_dir, 0755, true);
$log_path = $log_dir . ($config['log_file'] ?? 'b24_leads.log');

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$current_host = $_SERVER['HTTP_HOST'] ?? '';
$referer_raw = $_SERVER['HTTP_REFERER'] ?? '';
$origin_raw  = $_SERVER['HTTP_ORIGIN'] ?? '';

// === Helpers ===
function log_line($path, $line) {
    @file_put_contents($path, $line . "\n---\n", FILE_APPEND | LOCK_EX);
}

function tg_notify($config, $text) {
    if (empty($config['telegram_bot_token']) || empty($config['telegram_chat_id'])) {
        return;
    }
    $url = 'https://api.telegram.org/bot' . $config['telegram_bot_token'] . '/sendMessage';
    $payload = http_build_query([
        'chat_id'    => $config['telegram_chat_id'],
        'text'       => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => 'true',
    ]);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function he($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8'); }

// === 1. Метод ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=method_not_allowed method=' . $_SERVER['REQUEST_METHOD'] . ' ip=' . $ip);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    return;
}

// === 2. Origin / Referer ===
$allowed_hosts = array_merge(
    $config['test_hosts'] ?? [],
    ['obrprofi.ru', 'dev.obrprofi.ru']
);
$referer_host = parse_url($referer_raw, PHP_URL_HOST) ?: '';
$origin_host  = parse_url($origin_raw,  PHP_URL_HOST) ?: '';
$check_host   = $referer_host ?: $origin_host;
$origin_ok    = false;
foreach ($allowed_hosts as $h) {
    if ($check_host !== '' && stripos($check_host, $h) !== false) {
        $origin_ok = true;
        break;
    }
}
if (!$origin_ok) {
    $details = 'referer=' . ($referer_raw ?: '(empty)') . ' origin=' . ($origin_raw ?: '(empty)');
    log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=invalid_origin ip=' . $ip . ' ' . $details);
    tg_notify($config, "🟠 <b>ОТКЛОНЕНО: invalid_origin</b>\n" .
        "Referer: <code>" . he($referer_raw ?: '(пусто)') . "</code>\n" .
        "Origin: <code>"  . he($origin_raw  ?: '(пусто)') . "</code>\n" .
        "IP: <code>" . he($ip) . "</code>\n" .
        "UA: " . he(mb_substr($ua, 0, 100)));
    echo json_encode(['ok' => false, 'error' => 'invalid_origin']);
    return;
}

// === 3. Honeypot (silent — это бот) ===
$honeypot = trim($_POST['website'] ?? '');
if ($honeypot !== '') {
    log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [BOT-REJECT] reason=honeypot val=' . mb_substr($honeypot, 0, 100) . ' ip=' . $ip);
    echo json_encode(['ok' => true, 'lead_id' => 0]);
    return;
}

// === 4. Time-check (silent — тоже бот) ===
$form_open_at = (int)($_POST['form_open_at'] ?? 0);
if ($form_open_at > 0) {
    $now_ms = (int)(microtime(true) * 1000);
    $elapsed_ms = $now_ms - $form_open_at;
    if ($elapsed_ms < 1500) {
        log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [BOT-REJECT] reason=too_fast elapsed=' . $elapsed_ms . 'ms ip=' . $ip);
        echo json_encode(['ok' => false, 'error' => 'too_fast']);
        return;
    }
}

// === 5. Чтение полей ===
$name        = mb_substr(trim($_POST['name']        ?? ''), 0, 100);
$phone       = mb_substr(trim($_POST['phone']       ?? ''), 0, 30);
$email       = mb_substr(trim($_POST['email']       ?? ''), 0, 100);
$message     = mb_substr(trim($_POST['message']     ?? ''), 0, 2000);
$channel     = mb_substr(trim($_POST['channel']     ?? 'phone'), 0, 20);
$form_source = mb_substr(trim($_POST['form_source'] ?? 'Общий CTA'), 0, 500);
$page_title  = mb_substr(trim($_POST['page_title']  ?? ''), 0, 500);
$page_url    = mb_substr(trim($_POST['page_url']    ?? ''), 0, 1000);

$is_b2b      = (string)($_POST['b2b'] ?? '') === '1';
$inn         = preg_replace('/\D+/', '', (string)($_POST['inn'] ?? ''));
$employees   = mb_substr(trim($_POST['employees'] ?? ''), 0, 30);

$allowed_channels = ['phone', 'email', 'wa', 'tg', 'viber', 'max'];
if (!in_array($channel, $allowed_channels, true)) $channel = 'phone';

$phone_digits = preg_replace('/\D+/', '', $phone);

$channel_labels = [
    'phone' => 'Звонок', 'email' => 'Email',
    'wa' => 'WhatsApp', 'tg' => 'Telegram',
    'viber' => 'Viber', 'max' => 'MAX',
];
$channel_label = $channel_labels[$channel];

// === 6. Валидация ===
if ($is_b2b) {
    if (strlen($inn) !== 10 && strlen($inn) !== 12) {
        log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=invalid_inn inn=' . $inn . ' ip=' . $ip);
        tg_notify($config, "🟠 <b>ОТКЛОНЕНО: invalid_inn</b>\nИНН: <code>" . he($inn) . "</code> (длина " . strlen($inn) . ")\nТелефон: " . he($phone));
        echo json_encode(['ok' => false, 'error' => 'invalid_inn']);
        return;
    }
    if ($employees === '') {
        log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=employees_required ip=' . $ip);
        tg_notify($config, "🟠 <b>ОТКЛОНЕНО: employees_required</b>\nB2B-форма, ИНН " . he($inn) . ", телефон " . he($phone));
        echo json_encode(['ok' => false, 'error' => 'employees_required']);
        return;
    }
}

if ($channel === 'email') {
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=invalid_email email=' . $email . ' ip=' . $ip);
        tg_notify($config, "🟠 <b>ОТКЛОНЕНО: invalid_email</b>\nEmail: <code>" . he($email) . "</code>\nСтраница: " . he($page_title));
        echo json_encode(['ok' => false, 'error' => 'invalid_email']);
        return;
    }
} else {
    if (strlen($phone_digits) < 10) {
        log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=invalid_phone phone=' . $phone . ' ip=' . $ip);
        tg_notify($config, "🟠 <b>ОТКЛОНЕНО: invalid_phone</b>\nТелефон: <code>" . he($phone) . "</code> (цифр: " . strlen($phone_digits) . ")\nСтраница: " . he($page_title));
        echo json_encode(['ok' => false, 'error' => 'invalid_phone']);
        return;
    }
}

if (function_exists('apcu_fetch')) {
    $rate_key = 'b24_lead_' . md5($ip . '|' . $phone_digits);
    if (apcu_fetch($rate_key)) {
        log_line($log_path, '[' . date('Y-m-d H:i:s') . '] [REJECT] reason=rate_limit ip=' . $ip . ' phone=' . $phone);
        tg_notify($config, "🟡 <b>RATE-LIMIT: повторная заявка</b>\nТелефон: " . he($phone) . "\nIP: " . he($ip) . "\nЭто наша защита, перезвонить клиенту вручную.");
        echo json_encode(['ok' => false, 'error' => 'rate_limit']);
        return;
    }
    apcu_store($rate_key, 1, (int)$config['rate_limit_seconds']);
}

// === 7. Подготовка для B24 ===
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
if ($form_source !== '') $comments_lines[] = 'Источник на сайте: ' . $form_source;
if ($page_title !== '')  $comments_lines[] = 'Страница: ' . $page_title;
if ($page_url !== '')    $comments_lines[] = 'URL: ' . $page_url;
$comments_lines[] = 'Удобный канал связи: ' . $channel_label;
if ($message !== '') {
    $comments_lines[] = '';
    $comments_lines[] = 'Сообщение клиента:';
    $comments_lines[] = $message;
}
$comments = implode("\n", $comments_lines);

$source_description = 'Предпочитаемый канал связи: ' . $channel_label;

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
    'SOURCE_ID'          => 'RC_GENERATOR',
    'SOURCE_DESCRIPTION' => $source_description,
    'UF_CRM_LEAD_1737612814914' => $page_url,
    'UF_CRM_1738819361031'      => $page_title,
    'UF_CRM_1739333675514'      => $form_source,
    'UF_CRM_1740631494137'      => $device_type,
];
if ($phone !== '') $fields['PHONE'] = [['VALUE' => $phone, 'VALUE_TYPE' => 'WORK']];
if ($email !== '') $fields['EMAIL'] = [['VALUE' => $email, 'VALUE_TYPE' => 'WORK']];

// === 8. Лог [LIVE]/[TEST] перед отправкой ===
log_line($log_path,
    '[' . date('Y-m-d H:i:s') . '] '
    . ($is_test_host ? '[TEST] ' : '[LIVE] ')
    . 'host=' . $current_host . ' channel=' . $channel . ' phone=' . $phone . ' email=' . $email
    . "\n" . $title . "\n" . $comments
);

// === 9. Запрос к B24 ===
$webhook = rtrim($config['webhook_url'], '/');
$endpoint = $webhook . '/crm.lead.add.json';
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

// === 10. Хелпер: формирование TG-сообщения с данными лида ===
$build_tg_lead_text = function($status_emoji, $status_title, $extra_lines = []) use ($is_b2b, $title, $phone, $email, $channel_label, $form_source, $page_title, $page_url, $inn, $employees, $name, $message, $ip, $device_type) {
    $lines = [$status_emoji . ' <b>' . $status_title . '</b>'];
    if ($is_b2b) {
        $lines[] = '🏢 B2B: ИНН <code>' . he($inn) . '</code> · ' . he($employees) . ' сотр.';
    }
    if ($name !== '') $lines[] = '👤 ' . he($name);
    if ($phone !== '') $lines[] = '📞 <code>' . he($phone) . '</code> (' . he($channel_label) . ')';
    if ($email !== '') $lines[] = '✉️ ' . he($email);
    if ($form_source !== '') $lines[] = '📄 ' . he(mb_substr($form_source, 0, 200));
    if ($page_url !== '') $lines[] = '📍 <a href="' . he($page_url) . '">' . he(mb_substr($page_url, 0, 80)) . '</a>';
    if ($message !== '') $lines[] = '💬 ' . he(mb_substr($message, 0, 300));
    foreach ($extra_lines as $l) $lines[] = $l;
    $lines[] = '<i>' . date('d.m H:i') . ' · ' . he($device_type) . ' · IP ' . he($ip) . '</i>';
    return implode("\n", $lines);
};

// === 11. Обработка ответа ===
if ($response === false || $http_code !== 200) {
    log_line($log_path,
        '[' . date('Y-m-d H:i:s') . '] [B24-ERROR] http=' . $http_code . ' err=' . $curl_error . ' resp=' . substr((string)$response, 0, 500)
    );
    tg_notify($config, $build_tg_lead_text('🔴', 'ОШИБКА B24 — лид НЕ создан', [
        '❌ HTTP ' . $http_code . ($curl_error ? ' · ' . he($curl_error) : ''),
    ]));
    echo json_encode(['ok' => false, 'error' => 'b24_unavailable']);
    return;
}

$data = json_decode($response, true);
if (!is_array($data) || isset($data['error'])) {
    log_line($log_path,
        '[' . date('Y-m-d H:i:s') . '] [B24-ERROR] resp=' . substr($response, 0, 500)
    );
    tg_notify($config, $build_tg_lead_text('🔴', 'B24 ОТКЛОНИЛ — лид НЕ создан', [
        '❌ ' . he(substr($response, 0, 200)),
    ]));
    echo json_encode(['ok' => false, 'error' => 'b24_rejected']);
    return;
}

$lead_id = (int)($data['result'] ?? 0);
log_line($log_path,
    '[' . date('Y-m-d H:i:s') . '] [B24-OK] lead_id=' . $lead_id
);
tg_notify($config, $build_tg_lead_text('🟢', 'НОВАЯ ЗАЯВКА → B24 #' . $lead_id, [
    '✅ Лид создан в B24',
]));

echo json_encode(['ok' => true, 'lead_id' => $lead_id]);
