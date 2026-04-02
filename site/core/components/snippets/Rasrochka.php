<?php
$value = (int) preg_replace('/\D+/', '', $modx->getOption('value', $scriptProperties, 0));
$months = (int) $modx->getOption('months', $scriptProperties, 12);

if ($value <= 0 || $months <= 0) return '';

$monthly = round($value / $months);
return number_format($monthly, 0, '', ' ');