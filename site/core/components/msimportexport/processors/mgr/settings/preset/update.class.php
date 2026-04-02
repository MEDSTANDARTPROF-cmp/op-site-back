<?php

class msImportExportPresetUpdateProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;
    /** @var MsIeTools $tools */
    public $tools;
    /** @var  MsiePreset $preset */
    protected $preset = null;
    /** @var array $invalidFields */
    protected $invalidFields = array();

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $this->tools = $this->msie->getTools();
        return parent::initialize();
    }


    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    public function process()
    {
        $settings = $this->modx->fromJSON($this->getProperty('settings', '[]'));
        $id = $this->getProperty('id', 0);

        //  $this->modx->log(modX::LOG_LEVEL_ERROR,  var_export($settings,true));

        if (!$this->preset = $this->modx->getObject('MsiePreset', $id)) {
            return $this->failure($this->modx->lexicon('msimportexport_settings_err_save'));
        }

        if (!empty($settings)) {
            if (is_array($settings)) {
                $this->preset->setSettings($settings);
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error save settings! Data is not an array.');
            }
        }

        $fields = $this->getProperty('fields', null);
        if ($fields !== null) {
            $fields = $this->modx->fromJSON($fields);
            $fields = $this->prepareFields($fields);
            $this->preset->set('fields', $fields);
        }

        $object = null;
        if (!$this->preset->save()) {
            return $this->failure($this->modx->lexicon('msimportexport_settings_err_save'));
        }

        if ($fields && !$this->validateFields($fields)) {
            $object['warning'] = $this->modx->lexicon('msimportexport_settings_warning_invalid_fields', array(
                'fields' => implode('; ', $this->invalidFields)
            ));
        }

        return $this->success($this->modx->lexicon('msimportexport_settings_success_save'), $object);

    }

    /**
     * @param array $fields
     *
     * @return array
     */
    private function prepareFields(array $fields = array())
    {
        $result = array();

        foreach ($fields as $field) {
            if ($field == '-1') {
                $result[] = '';
            } else {
                $result[] = $field;
            }
        }

        return $result;
    }

    /**
     * @param array $fields
     *
     * @return bool
     */
    private function validateFields(array $fields = array())
    {
        $serviceFields = $this->getServiceFields();

        if ($serviceFields && $fields) {
            foreach ($fields as $key) {
                if ($key && !isset($serviceFields[$key])) {
                    $this->invalidFields[] = $key;
                }
            }
        }

        return !count($this->invalidFields) > 0;
    }

    /**
     * @return array
     */
    private function getServiceFields()
    {
        $fields = array();
        /** @var modProcessorResponse $response */
        $response = $this->msie->runProcessor('mgr/field/getlist', array(
            'id' => $this->preset->get('id')
        ));

        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $response->getMessage());
            return $fields;
        }
        $data = $this->tools->fromJSON($response->getResponse(), array());
        $results = $data['results'] ?? array();

        foreach ($results as $field) {
            $fields[$field['key']] = $field['alias'];
        }

        return $fields;
    }

}


return 'msImportExportPresetUpdateProcessor';