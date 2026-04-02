<?php

class msImportExportPresetGetProcessor extends modObjectGetProcessor
{
    public $languageTopics = array('msimportexport:preset');
    public $classKey = 'MsiePreset';

    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }


    public function cleanup()
    {
        $data = $this->object->toArray();
        $service = $this->msie->getService($this->object->get('mode'), $this->object->get('service'));
        $data['parent_class_key'] = $service ? $service->getParentClassKey() : 'modResource';
        $data['do_link'] = $this->msie->getDoUrl() . '?token=' . $this->object->get('token');
        return $this->success('', $data);
    }
}

return 'msImportExportPresetGetProcessor';