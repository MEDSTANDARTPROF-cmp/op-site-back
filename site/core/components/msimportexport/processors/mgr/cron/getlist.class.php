<?php

class msImportExportCronGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'MsieCron';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $checkListPermission = true;
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        // $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function beforeQuery()
    {
        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $c->leftJoin('MsiePreset', 'Preset', '`Preset`.`id` = `MsieCron`.`preset_id`');
        $c->select($this->modx->getSelectColumns('MsieCron', 'MsieCron'));
        $c->select($this->modx->getSelectColumns('MsiePreset', 'Preset', 'preset_', array('name', 'mode')));

        if ($query = $this->getProperty('query', '')) {
            $c->where(array(
                'Preset.name:LIKE' => '%' . $query . '%',
                'OR:description:LIKE' => '%' . $query . '%',
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

        /*$c->prepare();
        $this->modx->log(modX::LOG_LEVEL_ERROR, $c->toSQL());*/

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();
        if (!$this->getProperty('combo')) {
            $data['actions'] = array(
                array(
                    'cls' => array(
                        'menu' => 'green',
                        'button' => 'green',
                    ),
                    'icon' => 'icon icon-edit',
                    'title' => $this->modx->lexicon('msimportexport_cron_menu_update'),
                    'action' => 'cronUpdate',
                    'button' => true,
                    'menu' => true,
                ),
                array(
                    'cls' => array(
                        'menu' => 'red',
                        'button' => 'red',
                    ),
                    'icon' => 'icon icon-trash-o',
                    'title' => $this->modx->lexicon('msimportexport_cron_menu_remove'),
                    'multiple' => $this->modx->lexicon('msimportexport_cron_menu_multiple_remove'),
                    'action' => 'cronRemove',
                    'button' => true,
                    'menu' => true,
                ),
            );
        }

        return $data;


    }
}

return 'msImportExportCronGetListProcessor';