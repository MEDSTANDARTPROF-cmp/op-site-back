<?php

class msImportExportMenuPresetRemoveProcessor extends modObjectGetProcessor
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

        /** @var modProcessorResponse $response */
        $response = $this->modx->runProcessor('system/menu/remove', array(
            'text' => "msimportexport_menu_{$mode}_{$id}"
        ));

        if ($response->isError()) {
            return $this->failure($response->getMessage());
        }

        $this->tools->removeLexicon("msimportexport_menu_{$mode}_{$id}", 'default', $options);
        $this->tools->removeLexicon("msimportexport_menu_{$mode}_desc_{$id}", 'default', $options);
        $this->tools->removeLexicon("msimportexport_btn_run_{$id}", $mode, $options);
        $this->tools->removeLexicon("msimportexport_desc_run_{$id}", $mode, $options);

        return $this->success();
    }

}

return 'msImportExportMenuPresetRemoveProcessor';