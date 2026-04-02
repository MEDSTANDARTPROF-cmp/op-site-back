<?php
// Подключаем MODX
define('MODX_API_MODE', true);
require_once '/home/s/severmarin/obrprofi.ru/public_html/core/config/config.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('web');

// Путь к папке с JSON-файлами
$jsonDirectory = __DIR__ . '/data';

if (!is_dir($jsonDirectory)) {
    die('Директория с JSON-файлами не найдена.');
}

$jsonFiles = glob($jsonDirectory . '/*.json');

foreach ($jsonFiles as $jsonFile) {
    echo "Обрабатываю файл: $jsonFile\n";

    $rawData = file_get_contents($jsonFile);
    if ($rawData === false) {
        echo "Ошибка чтения файла: $jsonFile\n";
        continue;
    }

    $jsonData = json_decode($rawData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Ошибка декодирования JSON в файле: $jsonFile\n";
        continue;
    }

    $jsonEscaped = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    $alias = basename($jsonFile, '.json'); // Используем имя файла как артикул

    // Ищем товар в MiniShop2 по артикулу
    $product = $modx->getObject('msProductData', ['article' => $alias]);

    if (!$product) {
        echo "Товар с артикулом $alias не найден.\n";
        continue;
    }
    
    // Получаем ID товара
    $productId = $product->get('id');

    // Теперь получаем modResource (для работы с TV)
    $resource = $modx->getObject('modResource', $productId);
    //var_dump($resource, $alias);
    //exit();

    if (!$resource) {
        echo "Товар с айди $productId не найден.\n";
        continue;
    }

    $result = $resource->setTVValue(46, $jsonEscaped); // TV с ID 46

    if ($result) {
        echo "Данные для товара '$alias' успешно сохранены.\n";
    } else {
        echo "Ошибка сохранения данных для товара '$alias'.\n";
    }
}

echo "Импорт завершен!\n";
