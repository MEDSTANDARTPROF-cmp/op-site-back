<?php

class  msImportExportPresetImportProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;
    public $classKey = 'MsiePreset';

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
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
        if (empty($_FILES['file'])) {
            return $this->failure($this->modx->lexicon('msimportexport_system_err_upload_file'));
        }

        if (!$content = file_get_contents($_FILES['file']['tmp_name'])) {
            return $this->failure($this->modx->lexicon('msimportexport_system_err_open_file'));
        }

        if (!$data = $this->modx->fromJSON($content)) {
            return $this->failure($this->modx->lexicon('msimportexport_system_err_parse_file'));
        }

        foreach ($data as $item) {
            if (!$this->validate($item)) {
                $err = $this->modx->lexicon('msimportexport_system_err_invalid_data_format', array('data' => print_r($item, 1)));
                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
                continue;
            }

            $item['fields'] = $this->modx->fromJSON($item['fields']);
            $item['settings'] = $this->modx->fromJSON($item['settings']);
            
            /**@var MsiePreset $preset */
            $preset = $this->modx->newObject($this->classKey);
            $preset->fromArray($item);
            if (!$preset->save()) {
                $err = $this->modx->lexicon('msimportexport_preset_err_save', array('data' => print_r($item, 1)));
                $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            }
        }
        return $this->success('');
    }


    /**
     * @param array $data
     * @return bool
     */
    protected function validate($data = array())
    {
        $keys = array_keys($this->modx->getFields($this->classKey));
        foreach ($keys as $key) {
            if ($key == 'id') continue;
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }
        return true;
    }

}

return 'msImportExportPresetImportProcessor';