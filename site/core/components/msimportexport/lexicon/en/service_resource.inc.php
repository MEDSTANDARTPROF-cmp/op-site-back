<?php
/**
 * Service resource Russian Lexicon Entries for msImportExport
 *
 * @package msImportExport
 * @subpackage lexicon
 */

$_lang['msimportexport_resource_import_service_name'] = 'Импорт документов Modx';
$_lang['msimportexport_resource_import_service_description'] = 'Данный тип импорта позволяет как создать новые документы Modx если их еще нет, так и обновить уже имеющиеся.';
$_lang['msimportexport_resource_update_service_name'] = 'Быстрое обновление документов Modx';
$_lang['msimportexport_resource_update_service_description'] = 'Применяется только к документам которые уже созданы. Т.к обновления происходит без участия процессоров Modx то никакие вязанные с этим системные события не генерируются.';
$_lang['msimportexport_resource_import_service_setting_tab'] = 'Документ Modx';
$_lang['msimportexport_resource_import_service_setting_fieldset_alias'] = 'Алиас';
$_lang['msimportexport_resource_import_service_setting_fieldset_text_format'] = 'Форматирование текста полей';
$_lang['msimportexport_resource_import_service_setting_fieldset_default'] = 'Значение по умолчанию для новых документов';
$_lang['msimportexport_resource_import_service_setting_checking_field'] = 'Поле для проверки наличия импортируемого объекта';
$_lang['msimportexport_resource_import_service_setting_checking_field_help'] = 'По данному полю происходит поиск импортируемого объекта на сайте, поэтому значение в нем должно быть уникально.';
$_lang['msimportexport_resource_import_service_setting_check_existence'] = 'Проверять существование на сайте';
$_lang['msimportexport_resource_import_service_setting_check_existence_help'] = 'Отключение данной опции позволяет ускорить импорт но делать это стоит только если Вы уверены, что импортируемых обьектов точно нет на сайте.';
$_lang['msimportexport_resource_import_service_setting_parent_default'] = 'Родитель по умолчанию';
$_lang['msimportexport_resource_import_service_setting_parent_default_help'] = 'Укажите ресурс в которую будет добавлен новый импортируемый элемент если в файле импорта он не был передан (параметр parent) или небыл найдена';
$_lang['msimportexport_resource_import_service_setting_skip_empty_parent'] = 'Пропускать импорт нового ресурса если родитель не указан или не найден';
$_lang['msimportexport_resource_import_service_setting_skip_empty_parent_help'] = 'Если для ресурса не указан parent или не был найден и выбрано "Да" то импорт такого ресурса будет пропущен и информация о нем добавлена в лог. Если выбрано "Нет" то ресурс будет добавлен в  родителя по умолчанию';
$_lang['msimportexport_resource_import_service_setting_skip_action'] = 'Пропускать импорт если';
$_lang['msimportexport_resource_import_service_setting_skip_action_help'] = '';
$_lang['msimportexport_resource_import_service_setting_skip_empty_checking_field'] = 'Пропускать импорт если нет значения для поля проверки';
$_lang['msimportexport_resource_import_service_setting_skip_empty_checking_field_help'] = 'Если выбрано "Да" и в файле импорта в поле которое указано в опции "Поле для проверки наличия импортируемого объекта" будет пусто, то импорт такого ресурса будет пропущен.';
$_lang['msimportexport_resource_import_service_setting_check_unique_alias'] = 'Проверять алиас на уникальность';
$_lang['msimportexport_resource_import_service_setting_check_unique_alias_help'] = 'Выполняется дополнительная проверка на уникальность алиаса ресурса. В случае дублирования будет сгенерировано системное событие msieOnImportNotUnique. Включение данной опции замедляет скорость импорта!';
$_lang['msimportexport_resource_import_service_setting_create_unique_alias'] = 'Создавать уникальный алиас';
$_lang['msimportexport_resource_import_service_setting_create_unique_alias_help'] = 'Если включена опция "Проверять названия товара на дублирование" и возникает дублирование, то произойдет автоматическое создание уникального алиаса. Включение данной опции деактивирует возникновения события msieOnImportNotUnique.';
$_lang['msimportexport_resource_import_service_setting_template_unique_alias'] = 'Шаблон постфикса для алиаса';
$_lang['msimportexport_resource_import_service_setting_template_unique_alias_help'] = 'Укажите название поля которое следует добавить к алиасу что бы сделать его уникальным. Пример: [[+article]]. При пустом значении будет использовано уникальное 15 значное число.';
$_lang['msimportexport_resource_import_service_setting_use_alias_in_search'] = 'Использовать алиас в поиске ресурса по названию';
$_lang['msimportexport_resource_import_service_setting_use_alias_in_search_help'] = 'Поиск ресурса будет происходить как по названию так и по алиасу созданного из названия.';
$_lang['msimportexport_resource_import_service_setting_ctx'] = 'Контекст по умолчанию';
$_lang['msimportexport_resource_import_service_setting_ctx_help'] = 'Какой контекст будет присваиваться при создании нового ресурса, а также при поиске ресурса на сайте (включенная в системных настройках опция "Проверять на дублирование URI во всех контекстах" приводит к игнорированию выбранного контеста при поиске ресурса на сайте). Данная опция игнорируется если значение явно указан в файле импорта (параметр context_key). Если значение не указано в файле импорта и в опции то по умолчанию для нового ресурса будет использован web контекст, а поиск ресурса будет происходить по всем контекстам.';
$_lang['msimportexport_resource_import_service_setting_completion_action'] = 'Действие по окончанию импорта';
$_lang['msimportexport_resource_import_service_setting_completion_action_help'] = 'Укажите действие которое следует выполнить когда импорт будет завершен. Действия будут выполняться в выбранной последовательности.';
$_lang['msimportexport_resource_import_service_setting_text_format_method'] = 'Метод форматирования';
$_lang['msimportexport_resource_import_service_setting_text_format_method_help'] = '';
$_lang['msimportexport_resource_import_service_setting_text_format_fields'] = 'Список названий полей';
$_lang['msimportexport_resource_import_service_setting_text_format_fields_help'] = 'Укажите через запятую название полей текст которых следует форматировать';
$_lang['msimportexport_resource_import_service_setting_template_default'] = 'Шаблон по умолчанию';
$_lang['msimportexport_resource_import_service_setting_template_default_help'] = 'Какой шаблон будет присваиваться при создании нового документа Modx или категории товара. Данная опция игнорируется если значение явно указан в файле импорта категорий (параметр template).';
$_lang['msimportexport_resource_import_service_setting_published_default'] = 'Публиковать по умолчанию';
$_lang['msimportexport_resource_import_service_setting_published_default_help'] = 'Выберите «Да» если хотите, чтобы все новые документы Modx или категории товара сразу становились опубликованными. Данная опция игнорируется если значение явно указан в файле импорта категорий (параметр published).';
$_lang['msimportexport_resource_import_service_setting_hidemenu_default'] = 'Скрытавть из меню по умолчанию';
$_lang['msimportexport_resource_import_service_setting_hidemenu_default_help'] = 'Выберите «Да», для того чтобы параметр «Скрыть из меню» был выбран, при создании новых документов Modx или категорий товаров. Данная опция игнорируется если значение явно указан в файле импорта категорий (параметр hidemenu).';
$_lang['msimportexport_resource_import_service_setting_searchable_default'] = '«Доступен для поиска» по умолчанию';
$_lang['msimportexport_resource_import_service_setting_searchable_default_help'] = 'Выберите «Да» для того, чтобы сделать все новые документы Modx или категории товаров доступными для поиска по умолчанию. Данный опция игнорируется если значение явно указан в файле импорта категорий (параметр search).';
$_lang['msimportexport_resource_import_service_setting_disable_map_generation'] = 'Отключить генерацию карты ресурсов';
$_lang['msimportexport_resource_import_service_setting_disable_map_generation_help'] = 'Включение данной опции позволяет приблизительно в 3 раза ускорить обновления ресурса. <strong>Важно!</strong> Потенциально может вызвать <a href="https://Modx.pro/development/16327" target="_blank">проблемы</a> с работой сторонних дополнений/плагинов которые работают с событием обновления ресурса.';
$_lang['msimportexport_resource_import_service_setting_parent_delimiter'] = 'Разделитель для поля родитель (parent)';
$_lang['msimportexport_resource_import_service_setting_parent_delimiter_help'] = 'Укажите символ разделитель для поля parent который задан в виде цепочки из названий категорий. Если параметр не зада то будет использоваться значение из опции "Первый разделитель данных" ';
$_lang['msimportexport_resource_export_service_setting_published_only'] = 'Только опубликованные';
$_lang['msimportexport_resource_export_service_setting_published_only_help'] = '';
$_lang['msimportexport_resource_export_service_setting_exclude_deleted'] = 'Исключить удаленные';
$_lang['msimportexport_resource_export_service_setting_exclude_deleted_help'] = '';
$_lang['msimportexport_resource_export_service_setting_utm_source'] = 'Источник (utm_source)';
$_lang['msimportexport_resource_export_service_setting_utm_source_help'] = '';
$_lang['msimportexport_resource_export_service_setting_utm_medium'] = 'Канал (utm_medium)';
$_lang['msimportexport_resource_export_service_setting_utm_medium_help'] = '';
$_lang['msimportexport_resource_export_service_setting_utm_campaign'] = 'Кампания (utm_campaign)';
$_lang['msimportexport_resource_export_service_setting_utm_campaign_help'] = '';
$_lang['msimportexport_resource_export_service_setting_utm_help'] = 'Ко всем адресам (URL), содержащимся в файле, будут автоматически добавлены UTM-метки.';
$_lang['msimportexport_resource_export_service_setting_utm_extra_param'] = 'Дополнительные параметры в URL';
$_lang['msimportexport_resource_export_service_setting_utm_extra_param_help'] = 'Ко всем адресам (URL), содержащимся в файле, будут автоматически добавлена строка сформированная на основании шаблона параметров, разделенных символом &. <strong>Пример</strong>: my_param1=[[+id]]&my_param2=[[+pagetitle]]';
$_lang['msimportexport_resource_export_service_setting_fieldset_utm'] = 'UTM-метки';
$_lang['msimportexport_resource_export_service_name'] = 'Экспорт документов Modx';
$_lang['msimportexport_resource_export_service_description'] = '';
$_lang['msimportexport_resource_export_service_setting_tab'] = 'Документ Modx';




