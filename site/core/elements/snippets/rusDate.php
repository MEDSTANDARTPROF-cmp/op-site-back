$date = $modx->getOption('date', $scriptProperties, '');
if (!$date || $date == 0) return '';
setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia.1251');
return strftime('%e %B %Y', strtotime($date));