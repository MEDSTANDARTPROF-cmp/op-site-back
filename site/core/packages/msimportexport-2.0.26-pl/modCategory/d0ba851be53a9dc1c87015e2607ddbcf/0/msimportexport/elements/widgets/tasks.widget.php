<?php

class MsIeTasksWidget extends modDashboardWidgetInterface
{
    /** @var Msie $msie */
    protected $msie;
    public $cssBlockClass = 'msimportexport-widget';

    public function __construct(xPDO &$modx, modDashboardWidget &$widget, modManagerController &$controller)
    {
        parent::__construct($modx, $widget, $controller);
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $widgetHeight = $this->modx->getOption('msimportexport_widget_height', null, 'md', true);
        $this->cssBlockClass .= " msimportexport-widget--{$widgetHeight}";
    }


    public function render()
    {
        $this->controller->addLexiconTopic('msimportexport:default');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/msie.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/md5.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/moment.min.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/clipboard.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/checkboxgroup.js');

        $this->controller->addCss($this->msie->config['cssUrl'] . 'mgr/main.css');
        $this->controller->addCss($this->msie->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->controller->addCss($this->msie->config['assetsUrl'] . 'vendor/fontawesome/css/font-awesome.min.css');

        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/timer.panel.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/highcharts.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/exporting.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/highstock/modules/export-data.js');


        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.widget.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.grid.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.chart.js');
        //  $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.filter.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.report.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.panel.pid.js');
        $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/task/task.window.js');

        //  $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/crontime.field.js');
        //   $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/cron.grid.js');
        //  $this->controller->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/cron.window.js');

        $config = $this->msie->config;
        $config["daemonMode"] = $this->msie->getTools()->checkDaemonMode();
        $config["sys_settings"] = $this->msie->getTools()->getSysSettings();
        $config["show_hidden_settings"] = (int)$this->msie->getTools()->getOption('show_hidden_settings', 0);

        $this->controller->addHtml('<script>
            Ext.onReady(function() {
                Msie.config = ' . $this->modx->toJSON($config) . ';
                Msie.MODE_IMPORT = "' . Msie::MODE_IMPORT . '";
                Msie.MODE_EXPORT = "' . Msie::MODE_EXPORT . '";
            
                 MODx.load({
		            xtype: "msie-panel-task-widget",
		            renderTo: "msie-tasks-widget",
		        });
            });
        </script>');

        return '<div id="msie-tasks-widget"></div>';

    }
}

return 'MsIeTasksWidget';