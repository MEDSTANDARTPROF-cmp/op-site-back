<?php

class msImportExportPresetGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('msimportexport:default');
    public $classKey = 'MsiePreset';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $checkListPermission = true;
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        $service = $this->getProperty('service');
        $mode = $this->getProperty('mode');
        $query = $this->getProperty('query');

        if (!empty($service)) {
            $c->where(array('service' => $service));
        }

        if (!empty($mode)) {
            $c->where(array('mode' => $mode));
        }

        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%' . $query . '%',
                'OR:description:LIKE' => '%' . $query . '%'
            ));
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();
        $language = $this->modx->getOption('manager_language');
        $hasInMenu = $this->modx->lexicon->exists("msimportexport_menu_{$data['mode']}_{$data['id']}", $language);
        $data['has_menu'] = $hasInMenu;
        $managerUrl = $this->modx->getOption('manager_url');
        $data['run_page_url'] = $managerUrl . "?a=mgr/run&namespace=msimportexport&preset={$data['id']}";

        if (!$this->getProperty('combo')) {
            $data['actions'] = array(
                array(
                    'cls' => array(
                        'menu' => 'action-gray',
                        'button' => 'action-gray',
                    ),
                    'icon' => 'icon fa-cog',
                    'title' => $this->modx->lexicon('msimportexport_preset_menu_settings'),
                    'action' => 'updateItem',
                    'button' => true,
                    'menu' => true,
                ), array(
                    'cls' => array(
                        'menu' => 'green',
                        'button' => 'green',
                    ),
                    'icon' => 'icon icon-copy',
                    'title' => $this->modx->lexicon('msimportexport_preset_menu_duplicate'),
                    'action' => 'duplicateItem',
                    'button' => true,
                    'menu' => true,
                ), array(
                    'cls' => array(
                        'menu' => 'blue',
                        'button' => 'blue',
                    ),
                    'icon' => 'icon icon-upload',
                    'title' => $this->modx->lexicon('msimportexport_preset_menu_export'),
                    'multiple' => $this->modx->lexicon('msimportexport_preset_menu_multiple_export'),
                    'action' => 'exportItem',
                    'button' => false,
                    'menu' => true,
                )
            );

            if ($hasInMenu) {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'light-blue',
                        'button' => 'light-blue',
                    ),
                    'icon' => 'icon icon-pencil',
                    'title' => $this->modx->lexicon('msimportexport_preset_menu_update_menu'),
                    'multiple' => '',
                    'action' => 'updateMenu',
                    'button' => false,
                    'menu' => true,
                );
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'violet',
                        'button' => 'violet',
                    ),
                    'icon' => 'icon icon-external-link',
                    'title' => $this->modx->lexicon('msimportexport_preset_menu_open_run_page'),
                    'multiple' => '',
                    'action' => 'openRunPage',
                    'button' => true,
                    'menu' => true,
                );
            }
            array_push($data['actions'],
                array(
                    'cls' => array(
                        'menu' => 'orange',
                        'button' => 'orange',
                    ),
                    'icon' => 'icon icon-' . ($hasInMenu ? 'times' : 'bars'),
                    'title' => $hasInMenu
                        ? $this->modx->lexicon('msimportexport_preset_menu_remove_menu')
                        : $this->modx->lexicon('msimportexport_preset_menu_create_menu'),
                    'multiple' => '',
                    'action' => $hasInMenu ? 'removeMenu' : 'createMenu',
                    'button' => false,
                    'menu' => true,
                ),
                array(
                    'cls' => array(
                        'menu' => 'red',
                        'button' => 'red',
                    ),
                    'icon' => 'icon icon-trash-o',
                    'title' => $this->modx->lexicon('msimportexport_preset_menu_remove'),
                    'multiple' => $this->modx->lexicon('msimportexport_preset_menu_multiple_remove'),
                    'action' => 'removeItem',
                    'button' => true,
                    'menu' => true,
                ));
        }
        return $data;
    }
}

return 'msImportExportPresetGetListProcessor';