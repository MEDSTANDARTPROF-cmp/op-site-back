<?php
/**
 * IeMs2ProductExportService Russian Lexicon Entries for ieMs2
 *
 * @package iems2
 * @subpackage lexicon
 */

$_lang['iems2_iems2productexportservice_name'] = 'Экспорт товаров miniShop2';
$_lang['iems2_iems2productexportservice_description'] = '';
$_lang['iems2_iems2productexportservice_setting_tab'] = 'miniShop2';
$_lang['iems2_iems2productexportservice_setting_vendors'] = 'Производители';
$_lang['iems2_iems2productexportservice_setting_allow_price_modification'] = 'Разрешить изменение цены';
$_lang['iems2_iems2productexportservice_setting_allow_price_modification_help'] = 'Для каждого товара будет вызвано событие msOnGetProductPrice. Включение данной опции может замедлить экспорт.';
$_lang['iems2_iems2productexportservice_setting_price_format'] = 'Формат цен';
$_lang['iems2_iems2productexportservice_setting_price_format_help'] = 'Укажите, как нужно форматировать цены товаров функцией number_format(). Используется JSON строка с массивом для передачи 3х параметров: количество десятичных, разделитель десятичных и разделитель тысяч. По умолчанию формат [2,"."," "], что превращает "15336.6" в "15 336.60"';
$_lang['iems2_iems2productexportservice_setting_price_format_no_zeros'] = 'Убирать лишние нули в ценах';
$_lang['iems2_iems2productexportservice_setting_price_format_no_zeros_help'] = 'По умолчанию, цены товаров выводятся с двумя десятичными: "15.20". Если эта опция включена, лишние нули в конце цены убираются и вы получите "15.2".';
$_lang['iems2_iems2productexportservice_setting_multicategory_format'] = 'Мультикатегории в виде ID';
$_lang['iems2_iems2productexportservice_setting_multicategory_format_help'] = 'Если выбрано "Да" то список мультикатегорий будет в виде ID иначе в виде пути из их названий (в таком формате импорт медленней).';