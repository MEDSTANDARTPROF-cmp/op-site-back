<?php

class msImportExportWatcherGetListProcessor extends modProcessor
{

    /** @var Msie $msie */
    public $msie;
    /** @var  MsIeWatcher $watcher */
    protected $watcher;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $this->watcher = $this->msie->getWatcher();
        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    /**
     * @return array|string
     */
    public function process()
    {
        $list = array();
        if ($this->watcher) {
            if ($items = $this->watcher->getStates(false)) {
                foreach ($items as $item) {
                    $list[] = $this->prepareArrayRow($item);
                }
            }
        }
        return $this->outputArray($list, count($list));
    }

    public function prepareArrayRow(array $data)
    {
        $data['actions'] = array();
        $data['actions'][] = array(
            'cls' => array(
                'menu' => 'blue',
                'button' => 'blue',
            ),
            'icon' => 'icon icon-info',
            'title' => $this->modx->lexicon('msimportexport_watcher_menu_pid'),
            'action' => 'watcherPid',
            'button' => true,
            'menu' => true,
        );

        if ($data['status'] == MsIeWatcher::STATUS_RUNNING) {
            $data['actions'][] = array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('msimportexport_watcher_menu_remove'),
                'multiple' => $this->modx->lexicon('msimportexport_watcher_menu_remove'),
                'action' => 'watcherRemove',
                'button' => true,
                'menu' => true,
            );
        }
        return $data;
    }
}

return 'msImportExportWatcherGetListProcessor';