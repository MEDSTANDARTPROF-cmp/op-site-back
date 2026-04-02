<?php

$exists = $chunks = false;
$output = null;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:

        break;

    case xPDOTransport::ACTION_UPGRADE:

        /** @var array $scriptProperties */
        $corePath = $modx->getOption('quickview_core_path', null,
            $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/quickview/');
        /** @var QuickView $QuickView */
        $QuickView = $modx->getService('quickview', 'QuickView', $corePath . 'model/quickview/',
            array('core_path' => $corePath));
        if (
            $QuickView AND
            (!property_exists($QuickView, 'version') OR version_compare($QuickView->version, '2.0.0-beta', '<'))
        ) {
            switch ($modx->getOption('manager_language')) {
                case 'ru':
                    $output .= 'Вы уверены что вы хотите <b>обновить</b> компонент?
				<small>
				Новая версия потребует изменения формата вызова сниппетов компонента.
				</small>
			';
                    break;
                default:
                    $output .= 'Are you sure that you want <b>to update</b> the component?
				<small>
				The new version will require changing the format of the component\'s snippet call.
                </small>
			';
            }
        }

        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return $output;