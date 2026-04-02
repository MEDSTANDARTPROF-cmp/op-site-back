<?php
/**
 * @package msimportexport
 * @subpackage controllers
 */


class msImportExportManagerController extends modExtraManagerController
{
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');

        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/msie.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/md5.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/moment.min.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/clipboard.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->msie->config['jsUrl'] . 'mgr/misc/checkboxgroup.js');

        $this->addCss($this->msie->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->msie->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addCss($this->msie->config['assetsUrl'] . 'vendor/fontawesome/css/font-awesome.min.css');


        $config = $this->msie->config;
        $config["daemonMode"] = $this->msie->getTools()->checkDaemonMode();
        $config["sys_settings"] = $this->msie->getTools()->getSysSettings();
        $config["show_hidden_settings"] = (int)$this->msie->getTools()->getOption('show_hidden_settings', 0);

        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Msie.config = ' . $this->modx->toJSON($config) . ';
            Msie.MODE_IMPORT = "' . Msie::MODE_IMPORT . '";
            Msie.MODE_EXPORT = "' . Msie::MODE_EXPORT . '";
        });
        </script>');

        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    public function checkPermissions()
    {
        return true;
    }

    /**
     * @param string $script
     *
     * @return void
     */
    public function addJavascript($script)
    {
        $version = '?v=' . dechex(crc32($this->msie->version));
        parent::addJavascript($script . $version);
    }

    /**
     * @param string $script
     *
     * @return void
     */
    public function addCss($script)
    {
        $version = '?v=' . dechex(crc32($this->msie->version));
        parent::addCss($script . $version);
    }
}