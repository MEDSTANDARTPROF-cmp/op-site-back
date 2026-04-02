<?php
/**
 * @var modX $modx
 * @var array $options
 * @var xPDOTransport $transport
 */

$output = '';
$exists = $chunks = false;
$lang = $modx->getOption('manager_language');
if ($lang != 'ru') {
    $lang = 'en';
}


$additions = file_get_contents('https://raw.githubusercontent.com/Prihod/ie-additions/main/list.json');
$additions = json_decode($additions,true);

if (!function_exists('isFirstVersion')) {
    function isFirstVersion()
    {
        global $modx;
        try {
            $msie = $modx->getService('msimportexport', 'Msie');
            if ($msie && $msie->version) {
                $part = explode('.', $msie->version);
                $num = (int)$part[0];
                return $num === 1;
            }
        } catch (Exception $e) {
        }

        return true;
    }
}

if (!function_exists('getWarningForFirstVersion')) {
    function getWarningForFirstVersion($lang)
    {
        $test = '<p><span style="color: red; font-weight:bold;">Warning! You are trying to update to a second version of a package that is not compatible with the first.</span> <a href="https://docs.modx.pro/components/msimportexport/msimportexport-2#perehod-s-pervoy-versii-na">More details.</a></p>';
        if ($lang == 'ru') {
            $test = '<p><span style="color: red; font-weight:bold;">Предупреждение! Вы пытаетесь обновиться на вторую версию пакета которая не совместима с первой</span>. <a href="https://docs.modx.pro/components/msimportexport/msimportexport-2#perehod-s-pervoy-versii-na">Подробнее.</a></p>';
        }
        return $test."<br>";
    }
}


switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        break;
    case xPDOTransport::ACTION_UPGRADE:
        if (isFirstVersion()) {
            $output =  getWarningForFirstVersion($lang);

        }
        if (!empty($options['attributes']['chunks'])) {
            $chunks = '<ul id="formCheckboxes" style="height:200px;overflow:auto;">';
            foreach ($options['attributes']['chunks'] as $v) {
                $chunks .= '
                <li>
                    <label>
                        <input type="checkbox" name="update_chunks[]" value="' . $v . '"> ' . $v . '
                    </label>
                </li>';
            }
            $chunks .= '</ul>';
        }
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

$hasInstallAdditions = false;
$installAdditions = '<ul id="formAdditions" style="overflow:auto;">';

if ($additions && is_array($additions)) {
    foreach ($additions as $key => $item) {
        $exists = $modx->getObject('transport.modTransportPackage', array('package_name' => $key));
        if ($exists) continue;
        $hasInstallAdditions = true;
        $installAdditions .= '
                <li>
                    <label>
                        <input type="checkbox"  name="install_additions[]" value="' . $key . '|' . $item['version'] . '"> ' . $key . ' ' . $item['version'] . '
                         <div><small style="margin-left: 17px; font-size: 11px;">' . $item['desc'][$lang] . '</small></div>
                    </label>
                </li>';
    }
}

$installAdditions .= '</ul>';

if ($hasInstallAdditions) {
    switch ($lang) {
        case 'ru':
            $output .= 'Выберите пакеты, которые следует также установить:';
            $output .= '<br/>
                <small>
                    <a href="#" onclick="Ext.get(\'formAdditions\').select(\'input\').each(function(v) {v.dom.checked = true;});">отметить все</a> |
                    <a href="#" onclick="Ext.get(\'formAdditions\').select(\'input\').each(function(v) {v.dom.checked = false;});">cнять отметки</a>
                </small>
            ';
            break;
        default:
            $output .= 'Select packages to install as well:';
            $output .= '<br/>
                <small>
                    <a href="#" onclick="Ext.get(\'formAdditions\').select(\'input\').each(function(v) {v.dom.checked = true;});">select all</a> |
                    <a href="#" onclick="Ext.get(\'formAdditions\').select(\'input\').each(function(v) {v.dom.checked = false;});">deselect all</a>
                </small>
            ';
    }

    $output .= $installAdditions . '<br/><br/>';
}

if ($chunks) {
    switch ($lang) {
        case 'ru':
            $output .= 'Выберите чанки, которые нужно <b>перезаписать</b>:<br/>
                <small>
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">отметить все</a> |
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">cнять отметки</a>
                </small>
            ';
            break;
        default:
            $output .= 'Select chunks, which need to <b>overwrite</b>:<br/>
                <small>
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = true;});">select all</a> |
                    <a href="#" onclick="Ext.get(\'formCheckboxes\').select(\'input\').each(function(v) {v.dom.checked = false;});">deselect all</a>
                </small>
            ';
    }

    $output .= $chunks;
}

return $output;
