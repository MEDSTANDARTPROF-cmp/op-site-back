<?php
/**
 * Setting Russian Lexicon Entries for ieGallery
 *
 * @package iegallery
 * @subpackage lexicon
 */
$_lang['area_iegallery_main'] = 'Основные настройки';
$_lang['area_iegallery_workers'] = 'Workers';

$_lang['setting_iegallery_tools_handler_class'] = 'Класс Tools';
$_lang['setting_iegallery_tools_handler_class_desc'] = '';
$_lang['setting_iegallery_iegalleryms2imagesexportservice_worker'] = 'Worker class for service ieGalleryMs2ImagesExportService';
$_lang['setting_iegallery_iegalleryms2imagesexportservice_worker_desc'] = '';
$_lang['setting_iegallery_iegalleryms2galleryexportservice_worker'] = 'Worker class for service ieGalleryMs2GalleryExportService';
$_lang['setting_iegallery_iegalleryms2galleryexportservice_worker_desc'] = '';
$_lang['setting_iegallery_iegalleryms2imagesimportservice_worker'] = 'Worker class for service ieGalleryMs2ImagesImportService';
$_lang['setting_iegallery_iegalleryms2imagesimportservice_worker_desc'] = '';
$_lang['setting_iegallery_iegalleryms2galleryimportservice_worker'] = 'Worker class for service ieGalleryMs2GalleryImportService';
$_lang['setting_iegallery_iegalleryms2galleryimportservice_worker_desc'] = '';

$_lang['iegallery_iegalleryexportservice_setting_tab'] = 'Фото-галерея';
$_lang['iegallery_iegalleryimportservice_setting_tab'] = 'Фото-галерея';
$_lang['iegallery_iegalleryexportservice_setting_gallery_type'] = 'Тип галереи';
$_lang['iegallery_iegalleryexportservice_setting_gallery_type_help'] = 'Опция используется только в сторонних сервисах. При пустом значении экспорт галереи будет игнорироваться.';
$_lang['iegallery_iegalleryexportservice_setting_image_delimiter'] = 'Разделитель изображений';
$_lang['iegallery_iegalleryexportservice_setting_image_delimiter_help'] = '';
$_lang['iegallery_iegalleryexportservice_setting_absolute_url'] = 'Абсолютный URL';
$_lang['iegallery_iegalleryexportservice_setting_absolute_url_help'] = '';
$_lang['iegallery_iegalleryexportservice_setting_concatenate_images'] = 'Объединить изображения';
$_lang['iegallery_iegalleryexportservice_setting_concatenate_images_help'] = 'Данные из полей url и file для каждого ресурса галереи будут объединены через "Разделитель изображений".';
$_lang['iegallery_iegalleryexportservice_setting_copy_image'] = 'Копировать изображения';
$_lang['iegallery_iegalleryexportservice_setting_copy_image_help'] = 'Все экспортируемые изображения будут скопированы в указанную директорию.';
$_lang['iegallery_iegalleryexportservice_setting_copy_image_path'] = 'Путь к директории копирования';
$_lang['iegallery_iegalleryexportservice_setting_copy_image_path_help'] = 'Укажите путь относительно корня сайта к директории куда будут скопированы изображения попавшие в экспорт. Можно использовать плейсхолдеры: {assets_path},{base_path},{core_path} и {task_id}';
$_lang['iegallery_iegalleryexportservice_setting_add_images_to_archive'] = 'Добавить изображения в архив';
$_lang['iegallery_iegalleryexportservice_setting_add_images_to_archive_help'] = 'При включении данноой опции, а также при включении опции "Заархивировать" все файлы изображений будут добавлены в архив.';
$_lang['iegallery_iegalleryexportservice_setting_attach_settings'] = 'Настройки изображения для вставки в Excel';
$_lang['iegallery_iegalleryexportservice_setting_attach_settings_help'] = 'thumb - название размера из источника файлов галереи, если он не указан или у товара нет фото в таком размере то будет использовано оригинальное фото ширина которого будет равна значению из параметра width.';

$_lang['iegallery_iegalleryimportservice_setting_limit'] = 'Лимит выборки фото';
$_lang['iegallery_iegalleryimportservice_setting_limit_help'] = 'Если значение не указано или равно 0, то выборка не ограничивается.';
$_lang['iegallery_iegalleryimportservice_setting_sortdir'] = 'Сортировать изображения';
$_lang['iegallery_iegalleryimportservice_setting_sortdir_help'] = 'Направление сортировки изображений. По умолчанию DESC';

$_lang['iegallery_iegalleryimportservice_setting_image_delimiter'] = 'Разделитель изображений';
$_lang['iegallery_iegalleryimportservice_setting_image_delimiter_help'] = '';
$_lang['iegallery_iegalleryimportservice_setting_remove_images'] = 'Удалить все изображения перед импортом';
$_lang['iegallery_iegalleryimportservice_setting_remove_images_help'] = 'Перед импортом у импортируемого ресурса будут удалены все изображения.';
$_lang['iegallery_iegalleryimportservice_setting_base_path_images'] = 'Базовый путь к директории с изображениями';
$_lang['iegallery_iegalleryimportservice_setting_base_path_images_help'] = 'Базовый путь к директории относительно которой будет происходить поиск изображений при импорте в галерею, по умолчанию это значение из MODX_BASE_PATH. Данный параметр игнорируется если в файле импорта путь к изображению начинается с "./" в таком случаии путь к изображению формируется относительно файла импорта.';
$_lang['iegallery_iegalleryimportservice_setting_resize_upload_image'] = 'Изменять размер загружаемого изображения';
$_lang['iegallery_iegalleryimportservice_setting_resize_upload_image_help'] = 'Размер изображения будет изменен согласно значениям maxUploadWidth и maxUploadHeight источника файла.';
$_lang['iegallery_iegalleryimportservice_setting_force_update'] = 'Обновлять данные существующих изображений';
$_lang['iegallery_iegalleryimportservice_setting_force_update_help'] = '';
$_lang['iegallery_iegalleryimportservice_setting_gallery_type'] = 'Тип галереи';
$_lang['iegallery_iegalleryimportservice_setting_gallery_type_help'] = 'Опция используется только в сторонних сервисах. При пустом значении импорт галереи будет игнорироваться.';

$_lang['iegallery_sortdir_asc'] = 'По возрастанию индекса (ASC)';
$_lang['iegallery_sortdir_desc'] = 'По убыванию индекса (DESC)';


