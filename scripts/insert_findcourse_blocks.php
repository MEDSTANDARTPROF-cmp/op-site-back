<?php
$db = new PDO('mysql:host=localhost;dbname=obrprofi', 'obrprofi', 'obrprofi');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ids = [3, 5, 14, 15, 16, 23, 24, 27];

$needle1 = '[[$catalogFooter?]]';
$needle2 = '[[$catalogFooterDPO?]]';
$insert  = "[[\$ObrFormFindCourse]]\n\n";

foreach ($ids as $id) {
    $row = $db->query("SELECT templatename, content FROM modx_site_templates WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
    if (!$row) continue;
    $content = $row['content'];

    if (strpos($content, 'ObrFormFindCourse') !== false) {
        echo "[=] $id ({$row['templatename']}) — уже есть\n";
        continue;
    }

    if (strpos($content, $needle1) !== false) {
        $content = str_replace($needle1, $insert . $needle1, $content);
    } elseif (strpos($content, $needle2) !== false) {
        $content = str_replace($needle2, $insert . $needle2, $content);
    } else {
        echo "[!] $id ({$row['templatename']}) — не нашёл точку вставки\n";
        continue;
    }

    $st = $db->prepare("UPDATE modx_site_templates SET content=:c WHERE id=:id");
    $st->execute([':c' => $content, ':id' => $id]);
    echo "[+] $id ({$row['templatename']}) — обновлён\n";
}
