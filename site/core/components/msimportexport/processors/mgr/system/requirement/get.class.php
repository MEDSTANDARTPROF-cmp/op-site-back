<?php

class msImportExportSystemRequirementGetProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;
    protected $minVersion;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $this->minVersion = $this->modx->getOption('msimportexport_min_php_version', null, '5.6.*');
        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    public function process()
    {
        $statusWatcher = 0;
        $countActiveWatchers = $this->getCountActiveWatchers();
        $watcherMaxCount = $this->msie->getTools()->getSysSetting('watcher_max_count', 1);
        $daemonMode = $this->msie->getTools()->checkDaemonMode();
        $pcntlSignal = $this->msie->getTools()->checkPcntlSignal();

        if ($countActiveWatchers) {
            $statusWatcher = $countActiveWatchers >= $watcherMaxCount ? 1 : 0;
        }

        $result = array(
            array(
                'level' => 1,
                'status' => $this->msie->getTools()->checkExec(),
                'label' => $this->modx->lexicon('msimportexport_system_requirement_exec'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_exec_help'),

            ),
            array(
                'level' => 1,
                'status' => $this->msie->getTools()->checkPhpInterpreter(),
                'label' => $this->modx->lexicon('msimportexport_system_requirement_php_interpreter'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_php_interpreter_help'),

            ), array(
                'level' => $countActiveWatchers == 0 ? 1 : 2,
                'status' => $statusWatcher,
                'label' => $this->modx->lexicon('msimportexport_system_requirement_watcher', array('total' => $countActiveWatchers, 'max' => $watcherMaxCount)),
                'title' => '',

            ), array(
                'level' => 2,
                'status' => $daemonMode,
                'label' => $this->modx->lexicon('msimportexport_system_requirement_daemon_mode'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_daemon_mode_help'),

            ),
            array(
                'level' => 2,
                'status' => $daemonMode && $pcntlSignal,
                'label' => $this->modx->lexicon('msimportexport_system_requirement_exec_signal_command'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_exec_signal_command_help'),

            ),
            array(
                'level' => 2,
                'status' => extension_loaded('zip'),
                'label' => $this->modx->lexicon('msimportexport_system_requirement_php_extension_php_zip'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_php_extension_php_zip_help'),

            ),
            array(
                'level' => 2,
                'status' => extension_loaded('xml'),
                'label' => $this->modx->lexicon('msimportexport_system_requirement_php_extension_php_xml'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_php_extension_php_xml_help'),

            ),
            array(
                'level' => 2,
                'status' => $pcntlSignal,
                'label' => $this->modx->lexicon('msimportexport_system_requirement_pcntl_signal'),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_pcntl_signal_help'),

            ), array(
                'level' => 1,
                'status' => $this->msie->getTools()->checkPhpVersionCli(),
                'label' => $this->modx->lexicon('msimportexport_system_requirement_php_version_cli', array('v' => $this->minVersion)),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_current_version', array('v' => $this->msie->getTools()->getPhpVersionCli())),

            ),
            array(
                'level' => 1,
                'status' => $this->msie->getTools()->checkPhpVersionSite(),
                'label' => $this->modx->lexicon('msimportexport_system_requirement_php_version_site', array('v' => $this->minVersion)),
                'title' => $this->modx->lexicon('msimportexport_system_requirement_current_version', array('v' => $this->msie->getTools()->getPhpVersionSite())),

            ),
        );

        return $this->success('', $result);
    }

    protected function checkSysvshm()
    {
        return $this->msie->getTools()->checkFunctionEnabled('sem_get');
    }

    /**
     * @return int
     */
    protected function getCountActiveWatchers()
    {
        if ($watcher = $this->msie->getWatcher()) {
            return $watcher->getCount(false);
        }
        return 0;
    }

}

return 'msImportExportSystemRequirementGetProcessor';
