<?php

class msImportExportFieldGetListProcessor extends modObjectGetProcessor
{
    /** @var Msie $msie */
    public $msie;
    public $languageTopics = array('msimportexport:default');
    public $classKey = 'MsiePreset';
    /** @var MsiePreset $object */
    public $object;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    /**
     * @param string $key
     * @param string $prefixKey
     * @param string $label
     * @return array
     */
    public function getCheckingField($key, $prefixKey = '', $label = '')
    {
        if (!$field = $this->msie->getTools()->getResourceFields($prefixKey, $label, array($key))) {
            $field = $this->msie->getTools()->getProductFields($prefixKey, $label, array($key));
        }
        return $field;
    }

    /**
     * @return array
     */
    public function getList()
    {
        $result = array();
        $mode = $this->object->get('mode');
        $serviceName = $this->object->get('service');
        $query = trim($this->getProperty('query', ''));
        $checking = trim($this->getProperty('checking', false));
        try {
            /** @var MsIeService $service */
            $service = $this->msie->getService($mode, $serviceName);
            if ($service) {
                if (!$service->isEnabled()) return $result;
                $fields = $service->getFields($checking);
                $response = $this->modx->invokeEvent('msieOnGetServiceFields', array(
                    'mode' => $mode,
                    'msie' => $this->msie,
                    'service' => $service,
                    'fields' => $fields,
                    'checking' => $checking,
                ));
                if (is_array($response) && !empty($response)) {
                    foreach ($response as $val) {
                        if (!empty($val) && is_array($val)) {
                            $fields = array_merge($fields, $val);
                        }
                    }
                }
                if (!empty($fields)) {
                    foreach ($fields as $key => $field) {
                        if (!empty($query)) {
                            if (
                                !preg_match('/' . $query . '/iu', $key) &&
                                !preg_match('/' . $query . '/iu', $field['alias'])
                            ) {
                                continue;
                            }
                        }
                        $result[] = $field;
                    }

                    if (!empty($query) && empty($result)) {
                        $result[] = array('key' => $query, 'name' => $query, 'alias' => '', 'label' => '');
                    }
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Error load service: {$serviceName}");
            }
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
        }
        return $result;
    }

    /**
     * @return array|mixed|string
     */
    public function process()
    {
        $list = $this->getList();
        return $this->outputArray($list, count($list));
    }
}

return 'msImportExportFieldGetListProcessor';
