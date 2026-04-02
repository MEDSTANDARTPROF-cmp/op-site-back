<?php

class IeMs2LinksImportWorker extends MsIeResourceImportWorker
{
    /** @var string $classKey */
    protected $classKey = 'msProduct';
    /** @var bool $isRemoveLinks */
    protected $isRemoveLinks = false;

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->stats = array('errors' => 0, 'created' => 0);
            $this->processorsPath = MODX_CORE_PATH . 'components/minishop2/processors/';
            $this->isRemoveLinks = $this->getSetting('remove_links', false);
        }
        return $initialized;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $result = array();
        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(), 1));
        }
        $this->action = 'create';
        $settings = $this->getSettings();
        $checkingField = $this->getSetting('checking_field', '');

        foreach ($this->getFields() as $index => $field) {
            $result[$field] = $data[$index];
        }

        $masterKey = 'master_' . $checkingField;
        $slaveKey = 'slave_' . $checkingField;

        if (!isset($result[$masterKey]) || !isset($result[$slaveKey]) || !isset($result['link'])) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Error import link, incorrect data.' . print_r($data, 1));
            $this->incrStatsRecord('errors');
            return array();
        }

        if (!$masterProduct = $this->findResource($checkingField, $result[$masterKey], $this->classKey, $settings)) {
            $this->incrStatsRecord('errors');
            $err = $this->modx->lexicon('msimportexport_import_err_resource_nf', array('key' => $checkingField, 'value' => $result[$masterKey]));
            $this->tools->log($err);
            return array();
        }

        if (!$slaveProduct = $this->findResource($checkingField, $result[$slaveKey], $this->classKey, $settings)) {
            $this->incrStatsRecord('errors');
            $err = $this->modx->lexicon('msimportexport_import_err_resource_nf', array('key' => $checkingField, 'value' => $result[$masterKey]));
            $this->tools->log($err);
            return array();
        }

        return array(
            'link' => $result['link'],
            'master' => $masterProduct->get('id'),
            'slave' => $slaveProduct->get('id'),
        );
    }

    /**
     * @param array $data
     */
    public function work(array $data = array())
    {
        if (empty($data)) return;

        $response = $this->fireEvent('msieOnBeforeImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data));
        if (!is_array($response)) {
            if ($response === false) {
                $this->incrStatsRecord('errors');
            }
            return;
        }

        $data = $response['data'];

        if ($this->debug) {
            $record = print_r($this->getReadRecord(), 1);
            $this->debug("Import before run processor.\n\naction: {$this->action}\nfile record: {$record}\nparams: " . print_r($data, 1));
        }

        if (
            $this->isRemoveLinks &&
            !$this->storage->hasKeyInStore('ids', $data['master'])
        ) {
            if ($this->debug) {
                $this->debug('remove all links' . print_r($data, 1));
            }
            $this->removeLinks($data);
        }

        /** @var modProcessorResponse $response */
        $this->modx->error->reset();
        $response = $this->runProcessor('mgr/product/productlink/create', $data);
        if ($response->isError()) {
            $err = $this->modx->lexicon('msimportexport_import_err_import',
                array(
                    'action' => $this->action,
                    'message' => $response->getMessage(),
                    'info' => print_r($data, 1) . "\n" . print_r($response->getAllErrors(), 1)
                )
            );

            $this->tools->log($err);
            $this->incrStatsRecord('errors');
            return;
        }

        $object = $response->getObject();

        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }

        $this->incrStatsRecord($this->action . 'd');
        $response = $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));

        if (!is_array($response)) {
            return;
        }

        $this->storage->pushStore('ids', $data['master'], $data['master']);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function removeLinks(array $data)
    {
        $this->modx->error->reset();
        $response = $this->runProcessor('mgr/product/productlink/remove', $data);
        if ($response->isError()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($response->getAllErrors(), 1));
            return false;
        }
        return true;
    }


}