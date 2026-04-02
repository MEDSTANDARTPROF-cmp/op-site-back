<?php

class msImportExportMenuPresetGetProcessor extends modObjectGetProcessor
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
        $this->modx->lexicon->load("msimportexport:{$mode}");
        $key = "msimportexport_menu_{$mode}_{$id}";
        $data = array(
            'id' => $id,
            'parent' => $this->getParentMenu($key),
            'name' => $this->modx->lexicon($key),
            'description' => $this->modx->lexicon("msimportexport_menu_{$mode}_desc_{$id}"),
            'btn_text' => $this->modx->lexicon("msimportexport_btn_run_{$id}"),
            'page_text' => $this->modx->lexicon("msimportexport_desc_run_{$id}"),
        );
        return $this->success('', $data);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getParentMenu($key)
    {
        $parent = '';
        $menu = $this->modx->getObject('modMenu', array('text' => $key));
        if ($menu) {
            $parent = $menu->get('parent');
        }
        return $parent;

    }

}

return 'msImportExportMenuPresetGetProcessor';