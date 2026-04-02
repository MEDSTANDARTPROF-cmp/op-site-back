<?php

class msImportExportTaskGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'MsieTask';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $checkListPermission = true;
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;
    /** @var bool $isSignal */
    protected $isSignal = false;
    /** @var bool $daemonMode */
    protected $daemonMode = false;


    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
        $this->daemonMode = $this->msie->getTools()->checkDaemonMode();
        $this->isSignal = $this->msie->getTools()->checkPcntlSignal() && $this->daemonMode;


    }

    public function beforeQuery()
    {
        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $time = $this->getProperty('time', 'start');
        $time = empty($time) ? 'start' : $time;


        $c->leftJoin('MsiePreset', 'Preset', '`Preset`.`id` = `MsieTask`.`preset_id`');
        $c->select($this->modx->getSelectColumns('MsieTask', 'MsieTask'));
        $c->select($this->modx->getSelectColumns('MsiePreset', 'Preset', 'preset_', array('name', 'mode')));

        $c->where(array(
            'deleted' => 0,
        ));
        if ($status = $this->getProperty('status', '')) {
            if ($status != 'not_completed') {
                $c->where(array(
                    'status' => $status,
                ));
            } else {
                $c->where(array(
                    'status:!=' => MsieTask::STATUS_COMPLETED,
                ));
            }
        }

        if ($query = $this->getProperty('query', '')) {
            $c->where(array(
                'Preset.name:LIKE' => '%' . $query . '%',
                'OR:label:LIKE' => '%' . $query . '%',
            ));
        }

        if ($preset = $this->getProperty('preset', '')) {
            $c->where(array(
                'preset_id:=' => $preset
            ));
        }

        if ($mode = $this->getProperty('mode', '')) {
            $c->where(array(
                'Preset.mode:=' => $mode
            ));
        }

        if ($creator = $this->getProperty('creator', 0)) {
            if ($creator == 1) {
                $c->where(array(
                    'cron_id:=' => 0
                ));
            } else {
                $c->where(array(
                    'cron_id:>' => 0
                ));
            }
        }

        if ($creator = $this->getProperty('cron_id', 0)) {
            $c->where(array(
                'cron_id:=' => $creator
            ));
        }

        if ($timeBegin = trim($this->getProperty('time_begin', ''))) {
            $c->andCondition(array(
                $time . '_time:>=' => strtotime($timeBegin),
            ), null, 1);
        }

        if ($timeEnd = trim($this->getProperty('time_end', ''))) {
            $c->andCondition(array(
                $time . '_time:<=' => strtotime($timeEnd),
            ), null, 1);
        }

        /*$c->prepare();
        $this->modx->log(modX::LOG_LEVEL_ERROR, $c->toSQL());*/

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();
        $report = $object->getReport(true);
        $data['total_time'] = 0;
        $data['iteration'] = 0;

        if (!empty($report) && !empty($report['iteration'])) {
            $data['iteration'] = $report['iteration'];
        }
        if ($data['start_time'] > 0 && $data['finish_time'] > 0) {
            $data['total_time'] = $data['finish_time'] - $data['start_time'];
        }
        if (!$this->getProperty('combo')) {
            $data['actions'] = array(
                array(
                    'cls' => array(
                        'menu' => 'action-gray',
                        'button' => 'action-gray',
                    ),
                    'icon' => 'icon fa-external-link',
                    'title' => $this->modx->lexicon('msimportexport_task_menu_open_page_' . $data['preset_mode']),
                    'action' => 'taskOpenPage',
                    'button' => true,
                    'menu' => true,
                )
            );

            if (in_array($data['status'], array(MsieTask::STATUS_INITIATED, MsieTask::STATUS_WAITING, MsieTask::STATUS_STOPPED, MsieTask::STATUS_FAILED))) {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'green',
                        'button' => 'green',
                    ),
                    'icon' => 'icon icon-play',
                    'title' => $this->modx->lexicon('msimportexport_task_menu_run'),
                    'action' => 'taskRun',
                    'button' => true,
                    'menu' => true,
                );
            } else if (in_array($data['status'], array(MsieTask::STATUS_RUNNING))) {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => $this->isSignal ? 'green' : 'red',
                        'button' => $this->isSignal ? 'green' : 'red',
                    ),
                    'icon' => 'icon icon-stop',
                    'title' => $this->modx->lexicon('msimportexport_task_menu_stop'),
                    'action' => $this->isSignal ? 'taskStop' : 'taskHardStop',
                    'button' => true,
                    'menu' => true,
                );
            }
            $data['actions'] = array_merge($data['actions'], array(
                array(
                    'cls' => array(
                        'menu' => 'blue',
                        'button' => 'blue',
                    ),
                    'icon' => 'icon icon-info',
                    'title' => $this->modx->lexicon('msimportexport_task_menu_report'),
                    'action' => 'taskReport',
                    'button' => true,
                    'menu' => true,
                ),
                array(
                    'cls' => array(
                        'menu' => 'orange',
                        'button' => 'orange',
                    ),
                    'icon' => 'icon icon-bug',
                    'title' => $this->modx->lexicon('msimportexport_task_menu_log'),
                    'action' => 'taskLog',
                    'button' => true,
                    'menu' => true,
                ),
            ));
            if (
                $this->isSignal ||
                !in_array($data['status'], array(MsieTask::STATUS_RUNNING, MsieTask::STATUS_WAITING_KILL))
            ) {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'red',
                        'button' => 'red',
                    ),
                    'icon' => 'icon icon-trash-o',
                    'title' => $this->modx->lexicon('msimportexport_task_menu_remove'),
                    'multiple' => $this->modx->lexicon('msimportexport_task_menu_multiple_remove'),
                    'action' => 'taskRemove',
                    'button' => true,
                    'menu' => true,
                );
            }
        }

        return $data;


    }
}

return 'msImportExportTaskGetListProcessor';