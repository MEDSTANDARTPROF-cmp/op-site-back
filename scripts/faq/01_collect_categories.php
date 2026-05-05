<?php
/**
 * Этап 1 для FAQ: собрать иерархию категорий курсов из MODX.
 *
 * Структура категорий (по меню):
 *   - Переподготовка (parent)
 *      - Промышленная безопасность
 *      - Пожарная безопасность
 *      - Охрана труда
 *   - Сертификация
 *   - Проведение СОУТ
 *   - Рабочие специальности
 *   - Аттестация
 *      - Охрана труда
 *      - Электробезопасность
 *   - Повышение квалификации (parent)
 *      - Промышленная безопасность
 *      - Охрана труда
 *      - Электробезопасность
 *      - Строительство
 *      - Первая помощь
 *      - Гостиничное дело и туризм
 *      - Транспортная безопасность
 *      - Экология
 *      - Радиационная безопасность
 *      - Теплоэнергетика
 *      - Пожарная безопасность
 *      - Гражданская оборона
 *
 * Скрипт находит все ресурсы которые используют шаблоны категорий
 * (3, 5, 14, 15, 16, 23, 24, 27) и формирует JSON со структурой.
 */
$db = new PDO('mysql:host=localhost;dbname=obrprofi', 'obrprofi', 'obrprofi');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$category_tpl_ids = [3, 5, 14, 15, 16, 23, 24, 27];
$in = implode(',', $category_tpl_ids);

$rows = $db->query("
    SELECT id, parent, alias, uri, pagetitle, longtitle, menutitle, template
    FROM modx_site_content
    WHERE template IN ($in)
      AND deleted = 0
      AND published = 1
    ORDER BY parent, menuindex
")->fetchAll(PDO::FETCH_ASSOC);

echo "Найдено ресурсов: " . count($rows) . "\n\n";

// Группируем по parent для иерархии
$byParent = [];
foreach ($rows as $r) {
    $byParent[$r['parent']][] = $r;
}

// Главные родители — те у кого parent=0 или parent не в списке наших
$tplById = [];
foreach ($rows as $r) $tplById[$r['id']] = $r;

// Корневые категории (родители первого уровня)
$root_aliases = [
    'perepodgotovka', 'sertifikatsiya', 'sout', 'rabochie-spetsialnosti',
    'attestaciya', 'povyshenie-kvalifikacii', 'kursyi-dpo'
];
$rootIds = [];
foreach ($rows as $r) {
    if (in_array($r['alias'], $root_aliases) && (int)$r['parent'] === 0) {
        $rootIds[] = (int)$r['id'];
    }
}
echo "Корневые разделы: " . implode(', ', array_map(function($id) use ($tplById) {
    return $tplById[$id]['alias'];
}, $rootIds)) . "\n\n";

// Собираем структуру: root -> [подкатегории]
$structure = [];
foreach ($rootIds as $rootId) {
    $root = $tplById[$rootId];
    $children = $byParent[$rootId] ?? [];
    $structure[] = [
        'id' => (int)$root['id'],
        'alias' => $root['alias'],
        'pagetitle' => $root['pagetitle'],
        'menutitle' => $root['menutitle'] ?: $root['pagetitle'],
        'uri' => $root['uri'],
        'children' => array_map(function ($c) {
            return [
                'id' => (int)$c['id'],
                'alias' => $c['alias'],
                'pagetitle' => $c['pagetitle'],
                'menutitle' => $c['menutitle'] ?: $c['pagetitle'],
                'uri' => $c['uri'],
            ];
        }, $children),
    ];
}

echo "=== Структура категорий ===\n";
foreach ($structure as $root) {
    echo "📁 [{$root['id']}] {$root['menutitle']} ({$root['uri']})\n";
    foreach ($root['children'] as $c) {
        echo "    └─ [{$c['id']}] {$c['menutitle']} ({$c['uri']})\n";
    }
    echo "\n";
}

// Сохраняем JSON
file_put_contents(__DIR__ . '/01_categories.json', json_encode($structure, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Сохранено: scripts/faq/01_categories.json\n";

// Краткая статистика
$total_subcategories = 0;
foreach ($structure as $root) $total_subcategories += count($root['children']);
echo "\nИтого: " . count($structure) . " корневых, " . $total_subcategories . " подкатегорий\n";
