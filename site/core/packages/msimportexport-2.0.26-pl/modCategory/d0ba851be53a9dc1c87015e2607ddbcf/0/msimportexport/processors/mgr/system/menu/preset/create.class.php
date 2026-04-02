<?php

class msImportExportMenuPresetCreateProcessor extends modObjectGetProcessor
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
        $language = $this->modx->getOption('manager_language');
        $options = array('language' => $language);

        $data = array(
            'action_id' => 'mgr/run',
            'text' => "msimportexport_menu_{$mode}_{$id}",
            'description' => "msimportexport_menu_{$mode}_desc_{$id}",
            'params' => '&preset=' . $this->object->get('id'),
            'parent' => $this->getProperty('parent'),
            'namespace' => 'msimportexport',
        );

        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('system/menu/create', $data);

        if ($response->isError()) {
            return $this->failure($response->getMessage());
        }


        $menuText = $this->getProperty('name');
        $menuDesc = $this->getProperty('description', '');
        $btnText = $this->getProperty('btn_text');
        $pageText = $this->getProperty('page_text', '');

        $this->tools->addLexicon("msimportexport_menu_{$mode}_{$id}", $menuText, 'default', $options);
        $this->tools->addLexicon("msimportexport_menu_{$mode}_desc_{$id}", $menuDesc, 'default', $options);
        $this->tools->addLexicon("msimportexport_btn_run_{$id}", $btnText, $mode, $options);
        $this->tools->addLexicon("msimportexport_desc_run_{$id}", $pageText, $mode, $options);


        return $this->success();
    }

}

return 'msImportExportMenuPresetCreateProcessor';