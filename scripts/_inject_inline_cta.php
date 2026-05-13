<?php
/**
 * Установка snippet boxInlineCTA + mass-инжекция [[!boxInlineCTA]] в content
 * 44 ПОТЕНЦИАЛьных блог-статей перед первым <h2>.
 *
 * Usage:
 *   php scripts/_inject_inline_cta.php --dry   # показать что будет вставлено + куда
 *   php scripts/_inject_inline_cta.php --apply # применить
 */
define('MODX_API_MODE', true);
require_once dirname(__DIR__).'/site/index.php';

$DRY = in_array('--dry', $argv);
$APPLY = in_array('--apply', $argv);
if (!$DRY && !$APPLY) { echo "Usage: php scripts/_inject_inline_cta.php --dry | --apply\n"; exit(1); }

// 1. Install/update snippet boxInlineCTA
if ($APPLY) {
    $body = file_get_contents(__DIR__.'/partner/boxInlineCTA.php');
    $body = preg_replace('/^<\?php\s*/', '', $body);
    $s = $modx->getObject('modSnippet', ['name'=>'boxInlineCTA']);
    if (!$s) {
        $s = $modx->newObject('modSnippet');
        $s->set('name', 'boxInlineCTA');
        $s->set('category', 0);
        $s->set('description', 'Inline CTA для блога: определяет направление по URI/title, рендерит компактный CTA-блок');
    }
    $s->set('snippet', $body);
    $s->save();
    echo "[1] snippet boxInlineCTA installed id=".$s->get('id')." (".strlen($body)." chars)\n\n";
}

// 2. Read 44 URLs from CSV
$csv = dirname(__DIR__).'/temp_back/blog_audit/blog_потенциал.csv';
$fp = fopen($csv, 'r');
fgetcsv($fp); // header
$targets = [];
while (($row = fgetcsv($fp)) !== false) {
    $uri = ltrim($row[0], '/');
    $targets[] = $uri;
}
fclose($fp);
echo "[2] CSV: ".count($targets)." статей\n\n";

// 3. Iterate, inject
$stats = ['changed'=>0, 'already'=>0, 'no_h2'=>0, 'not_found'=>0];
$samples = [];

foreach ($targets as $uri) {
    $r = $modx->getObject('modResource', ['uri'=>$uri]);
    if (!$r) { $stats['not_found']++; echo "  NOT FOUND: $uri\n"; continue; }

    $content = (string)$r->get('content');
    if (strpos($content, '[[!boxInlineCTA') !== false) {
        $stats['already']++;
        continue;
    }

    // Найти первый <h2> в content и вставить ПЕРЕД ним
    if (!preg_match('/<h2[\s>]/i', $content, $m, PREG_OFFSET_CAPTURE)) {
        $stats['no_h2']++;
        echo "  NO H2: id=".$r->get('id')." uri=$uri (len=".strlen($content).")\n";
        continue;
    }
    $h2_pos = $m[0][1];
    $new_content = substr($content, 0, $h2_pos)
                  . "[[!boxInlineCTA]]\n\n"
                  . substr($content, $h2_pos);

    if (count($samples) < 3) {
        $samples[] = [
            'id' => $r->get('id'),
            'uri' => $uri,
            'context' => substr($content, max(0, $h2_pos - 200), 400),
        ];
    }

    if ($APPLY) {
        $r->set('content', $new_content);
        $r->save();
    }
    $stats['changed']++;
}

echo "\n=== SUMMARY ===\n";
echo "  to-be-changed: ".$stats['changed']."\n";
echo "  already-injected: ".$stats['already']."\n";
echo "  no-h2 (skip): ".$stats['no_h2']."\n";
echo "  not-found: ".$stats['not_found']."\n";

if ($DRY) {
    echo "\n=== SAMPLES (where injection will happen) ===\n";
    foreach ($samples as $s) {
        echo "\n--- id=".$s['id']." ".$s['uri']." ---\n";
        echo preg_replace('/\s+/u', ' ', $s['context'])."\n";
    }
    echo "\nDRY mode — nothing written. Run --apply.\n";
} else {
    $modx->cacheManager->refresh();
    echo "\nCache refreshed.\n";
}
