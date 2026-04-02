<?php
/**
 * @package msimportexport
 * @subpackage controllers
 */

if (!class_exists('msImportExportManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class msImportExportMgrExportManagerController extends msImportExportManagerController
{

    public function loadCustomCssJs()
    {


        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/timer.panel.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/tree.resource.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/local.grid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/ext.ddfield.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/highcharts.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/exporting.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/export-data.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/importexport.panel.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/preset/preset.grid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/preset/preset.window.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/file/file.window.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/field/field.panel.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.form.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.field.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.export.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.chart.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.report.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.pid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.window.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/export/export.panel.js');

        $settings = array();
        $settings = array();
        $setupData = array();

        if (!empty($_GET['preset'])) {
            /** @var MsiePreset * */
            $preset = $this->modx->getObject('MsiePreset', (int)$_GET['preset']);
            if ($preset) {
                $setupData = [
                    'preset' => $preset->get('id'),
                    'service' => $preset->get('service'),
                ];
            }
        }

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Msie.config["mode"] = "' . Msie::MODE_EXPORT . '";
            Msie.config["settings"] = ' . $this->modx->toJSON($settings) . ';
            MODx.add({
              xtype: "msie-panel-export",
              settings:  ' . $this->modx->toJSON($settings) . ',
              setupData:  ' . $this->modx->toJSON($setupData) . ',
              });
        });
        </script>');

        /**@var MsIeService[] $services */
        $services = $this->msie->getServices(Msie::MODE_EXPORT);
        if ($services) {
            foreach ($services as $key => $service) {
                foreach ($service->getLexiconTopics() as $topic) {
                    $this->addLexiconTopic($topic);
                }
                foreach ($service->getJavaScripts() as $js) {
                    $this->addJavascript($js);
                }
                foreach ($service->getCss() as $css) {
                    $this->addCss($css);
                }
                if ($html = $service->getHtml()) {
                    $this->addHtml($html);
                }
            }
        }

        $this->modx->invokeEvent('msieOnManagerCustomCssJs', array('controller' => $this, 'page' => Msie::MODE_EXPORT));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('msimportexport_page_title_export');
    }
}