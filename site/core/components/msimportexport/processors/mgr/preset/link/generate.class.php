<?php

class msImportExportPresetLinkGenerateProcessor extends modObjectGetProcessor
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
        $token = $this->object->generateToken();
        $this->object->set('token', $token);
        if ($this->object->save()) {
            $data = $this->object->toArray();
            $data['do_link'] = $this->msie->getDoUrl() . '?token=' . $token;
            return $this->success('', $data);
        } else {
            $this->failure($this->modx->lexicon('msimportexport_preset_err_generate_link'));
        }
    }
}

return 'msImportExportPresetLinkGenerateProcessor';