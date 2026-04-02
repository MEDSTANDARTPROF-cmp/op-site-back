<?php
/**
 * @package msimportexport
 * @subpackage controllers
 */

if (!class_exists('msImportExportManagerController')) {
    require_once dirname(dirname(__FILE__)) . '/manager.class.php';
}


class msImportExportMgrSettingsManagerController extends msImportExportManagerController
{

    public function loadCustomCssJs()
    {

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/timer.panel.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/cron/crontime.field.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/watcher/watcher.grid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/watcher/watcher.window.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/watcher/watcher.panel.pid.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.form.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/setting/setting.panel.system.js');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/system/system.panel.requirement.js');

        $settings = $this->msie->getTools()->getSysSettings();

        if ($settings['watcher_max_count'] == '') {
            $settings['watcher_max_count'] = $this->msie->getTools()->checkDaemonMode() ? 1 : 6;
            $this->msie->getTools()->setOption('system_settings', $settings, '', true);
        }

        $settings['watcher_script_path'] = $this->msie->config['watcherScriptPath'];

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            MODx.add({
              xtype: "msie-panel-setting",
              settings:  ' . $this->modx->toJSON($settings) . '
              });
        });
        </script>');

        $this->modx->invokeEvent('msieOnManagerCustomCssJs', array('controller' => $this, 'page' => 'settings'));
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('msimportexport_page_title_settings');
    }
}