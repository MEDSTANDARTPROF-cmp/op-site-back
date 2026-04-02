<?php
/**
 * @package msimportexport
 * @subpackage controllers
 */

if (!class_exists('msImportExportManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}

class msImportexportMgrTaskManagerController extends msImportExportManagerController
{
    public function loadCustomCssJs()
    {

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/timer.panel.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/highcharts.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/exporting.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/export-data.js');


        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.grid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.chart.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.filter.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.report.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.pid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.window.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/crontime.field.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/cron.grid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/cron.window.js');


        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
              MODx.add({
              xtype: "msie-panel-task"
              });
            });
        </script>');
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('msimportexport_page_title_task');
    }
}