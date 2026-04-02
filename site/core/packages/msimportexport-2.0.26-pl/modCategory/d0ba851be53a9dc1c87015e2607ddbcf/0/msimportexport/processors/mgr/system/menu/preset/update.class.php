<?php

class msImportExportMenuPresetUpdateProcessor extends modObjectGetProcessor
{
    public $languageTopics = array('msimportexport:default');
    public $classKey = 'MsiePreset';

    /** @var Msie $msie */
    public $msie;
    /** @var MsIeTools */
    public $tools;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $this->tools = $this->msie->getTools();
        return parent::initialize();
    }


    public function cleanup()
    {
        $id = $this->object->get('id');
        $mode = $this->object->get('mode');
        $parent = $this->getProperty('parent');
        $language = $this->modx->getOption('manager_language');
        $options = array('language' => $language);
        $key = "msimportexport_menu_{$mode}_{$id}";
        $menuText = $this->getProperty('name');
        $menuDesc = $this->getProperty('description', '');
        $btnText = $this->getProperty('btn_text');
        $pageText = $this->getProperty('page_text', '');

        $this->updateParentMenu($key, $parent);
        $this->tools->updateLexicon("msimportexport_menu_{$mode}_{$id}", $menuText, 'default', $options);
        $this->tools->updateLexicon("msimportexport_menu_{$mode}_desc_{$id}", $menuDesc, 'default', $options);
        $this->tools->updateLexicon("msimportexport_btn_run_{$id}", $btnText, $mode, $options);
        $this->tools->updateLexicon("msimportexport_desc_run_{$id}", $pageText, $mode, $options);

        return $this->success();
    }

    /**
     * @param string $key
     * @param string $parent
     *
     * @return bool
     */
    public function updateParentMenu($key, $parent)
    {
        if (empty($parent)) return false;
        $menu = $this->modx->getObject('modMenu', array('text' => $key));
        if ($menu && $menu->get('parent') != $parent) {
            $menu->set('parent', $parent);
            return $menu->save();
        }
        return true;

    }

}

return 'msImportExportMenuPresetUpdateProcessor';