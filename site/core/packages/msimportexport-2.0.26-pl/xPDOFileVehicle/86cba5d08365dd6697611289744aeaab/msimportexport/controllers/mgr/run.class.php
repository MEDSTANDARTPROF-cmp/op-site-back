<?php
/**
 * @package msimportexport
 * @subpackage controllers
 */

if (!class_exists('msImportExportManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class msImportExportMgrRunManagerController extends msImportExportManagerController
{

    /** @var  MsiePreset $preset */
    protected $preset;

    public function initialize()
    {
        $presetId = $this->modx->getOption('preset', $_GET, 0);
        $this->preset = $this->modx->getObject('MsiePreset', $presetId);
        if (empty($this->preset)) {
            return $this->modx->lexicon('msimportexport_preset_err_nfs', array('id' => $presetId));
        }
        return parent::initialize();
    }

    public function loadCustomCssJs()
    {

        $mode = $this->preset->get('mode');
        $presetId = $this->preset->get('id');
        $showBtnTask = $this->modx->getOption('btn_task', $_GET, 0);
        $this->modx->lexicon->load("msimportexport:{$mode}");

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/timer.panel.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/ext.ddfield.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/highcharts.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/exporting.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/export-data.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/run.importexport.panel.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/file/file.window.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.chart.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.report.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.pid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.window.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/' . $mode . '/run.' . $mode . '.panel.js');


        $descRun = '';
        $language = $this->modx->getOption('manager_language');
        $btnRunText = $this->modx->lexicon("msimportexport_{$mode}_btn_{$mode}");
        $userBtnRunText = "msimportexport_btn_run_{$presetId}";
        $userDescRun = "msimportexport_desc_run_{$presetId}";

        if ($this->modx->lexicon->exists($userBtnRunText, $language)) {
            $btnRunText = $this->modx->lexicon($userBtnRunText);
        }

        if ($this->modx->lexicon->exists($userDescRun, $language)) {
            $descRun = $this->modx->lexicon($userDescRun);
        }

        $settings = array(
            'mode' => $mode,
            'preset' => $presetId,
            'format' => '',
            'file' => $this->preset->getSetting('file'),
            'showBtnTask' => $showBtnTask,
            'btnRunText' => $btnRunText,
            'descRun' => $descRun,
            'presetSettings' => $this->preset->getSettings(),
        );

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Msie.config["mode"] = "' . $mode . '";
            MODx.add({
              xtype: "msie-panel-run-' . $mode . '",
              settings:  ' . $this->modx->toJSON($settings) . '
              });
        });
        </script>');


        $this->modx->invokeEvent('msieOnManagerCustomCssJs', array(
            'controller' => $this,
            'page' => 'run',
            'preset' => $this->preset,
        ));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('msimportexport_page_title_run') . ' ' . $this->preset->get('mode') . ' (' . $this->preset->get('id') . ')';
    }
}