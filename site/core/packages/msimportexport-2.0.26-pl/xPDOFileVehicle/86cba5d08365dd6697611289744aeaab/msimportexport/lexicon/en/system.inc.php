<?php
/**
 * System Russian Lexicon Entries for msImportExport
 *
 * @package msImportExport
 * @subpackage lexicon
 */
$_lang['msimportexport_system_requirements'] = 'Системные требования';
$_lang['msimportexport_system_requirement_exec'] = 'Функция exec';
$_lang['msimportexport_system_requirement_exec_help'] = 'Данная функция необходима для запуска скриптов в фоновом режиме.';
$_lang['msimportexport_system_requirement_pcntl_signal'] = 'PHP расширение pcntl';
$_lang['msimportexport_system_requirement_pcntl_signal_help'] = 'Функция pcntl_signal необходима для мягкой остановки выполняемого скрипта.';
$_lang['msimportexport_system_requirement_daemon_mode'] = 'Процесс «демон»';
$_lang['msimportexport_system_requirement_daemon_mode_help'] = 'Скрип (процесс «демон») выполняется в фоновом режиме, что позволяет снять ограничение на время его выполнения.';
$_lang['msimportexport_system_requirement_php_interpreter'] = 'Путь к php интерпретатору';
$_lang['msimportexport_system_requirement_php_interpreter_help'] = 'Необходима для запуска скриптов в фоновом режиме.';
$_lang['msimportexport_system_requirement_php_version_site'] = 'Минимальная версия php [[+v]] (сайт)';
$_lang['msimportexport_system_requirement_php_version_cli'] = 'Минимальная версия php [[+v]] (cli)';
$_lang['msimportexport_system_requirement_php_extension_php_zip'] = 'PHP расширение: php_zip';
$_lang['msimportexport_system_requirement_php_extension_php_zip_help'] = 'Необходимо для работы с Excel файлами.';
$_lang['msimportexport_system_requirement_php_extension_php_xml'] = 'PHP расширение: php_xml';
$_lang['msimportexport_system_requirement_php_extension_php_xml_help'] = 'Необходимо для работы с Excel файлами.';
$_lang['msimportexport_system_requirement_watcher'] = 'Активные наблюдатели ([[+total]]/[[+max]])';
$_lang['msimportexport_system_requirement_current_version'] = 'Текущая версия [[+v]]';
$_lang['msimportexport_system_requirement_btn_check'] = 'Проверить';
$_lang['msimportexport_system_requirement_exec_signal_command'] = 'Отправка комманд процессу';
$_lang['msimportexport_system_requirement_exec_signal_command_help'] = 'Если разрешено создавать процессы демоны и установлено PHP расширение pcntl то появится возможность останавливать/возобновлять выполнение задач, а также удалять их во время выполнения. ';
$_lang['msimportexport_system_btn_save'] = 'Сохранить системные настройки';
$_lang['msimportexport_system_err_ext_file'] = 'Недопустимое расширения файла. Поддерживаются следующие расширения файлов: %s';
$_lang['msimportexport_system_err_convert_file'] = 'Не удалось сделать перекодирование файла %s в %s';
$_lang['msimportexport_system_err_upload_file'] = 'Не удалось загрузить файл: [[+file]]';
$_lang['msimportexport_system_err_unzip_file'] = 'При разархивировании файла произошла ошибка';
$_lang['msimportexport_system_err_ns_file'] = 'Укажите файл для загрузки.';
$_lang['msimportexport_system_err_nf_file'] = 'Не удалось найти файл: [[+file]]';
$_lang['msimportexport_system_err_nf_dir'] = 'Не удалось найти директорию: [[+path]]';
$_lang['msimportexport_system_err_nf_reader'] = 'Не удалось найти reader для файла: [[+file]]';
$_lang['msimportexport_system_err_read_file'] = 'Не удалось прочитать файла: [[+file]]';
$_lang['msimportexport_system_err_open_file'] = 'Не удалось открыть файл [[+file]] с данными.';
$_lang['msimportexport_system_err_parse_file'] = 'При разборе файла произошла ошибка.';
$_lang['msimportexport_system_err_invalid_data_format'] = 'Неверный формат данных. [[+data]]';
$_lang['msimportexport_system_file_type_csv'] = 'CSV';
$_lang['msimportexport_system_file_type_tsv'] = 'TSV';
$_lang['msimportexport_system_file_type_xlsx'] = 'Excel 2007 (xlsx)';
$_lang['msimportexport_system_file_type_xlsx2'] = 'Excel 2007 c графикой (xlsx)';
$_lang['msimportexport_system_file_type_xls'] = 'Excel 2003 (xls)';
$_lang['msimportexport_system_file_type_ods'] = 'OpenDocument (ods)';
$_lang['msimportexport_system_file_type_json'] = 'JSON';
$_lang['msimportexport_system_file_type_xml'] = 'XML';
$_lang['msimportexport_system_file_type_pdf'] = 'PDF';
