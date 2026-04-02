<?php

class ieMsProductRemainsImportWorker extends MsIeImportWorker
{
    /** @var bool $isRemoveRemains */
    protected $isRemoveRemains = false;
    /** @var array $options */
    protected $options = array();

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->removePrefixField = 'mspr_';
            $this->stats = array('errors' => 0, 'created' => 0, 'updated' => 0);
            $this->isRemoveRemains = $this->getSetting('mspr_remove_remains', false);
            $this->checkingField = $this->getSetting('checking_field', '');
            $this->options = $this->tools->explodeAndClean($this->modx->getOption('mspr_options', null, ''));
            if ($this->checkingField) {
                $this->addPrepareFieldMethod($this->checkingField, $this, 'prepareFieldCheckingValue');
            }
        }
        return $initialized;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $result = array('options' => array());
        if ($fields = $this->getFields()) {
            foreach ($fields as $index => $field) {
                $val = isset($data[$index]) ? $data[$index] : '';
                if ($methods = $this->getPrepareFieldMethods($field)) {
                    foreach ($methods as $method => $context) {
                        if (method_exists($context, $method)) {
                            $result = $context->$method($field, $val, $data, $result, $this);
                        } else {
                            $this->modx->log(modX::LOG_LEVEL_ERROR, "'{$method}' method not found for preparing the '{$field}' field");
                        }
                    }
                } else {
                    $field = preg_replace("/^{$this->removePrefixField}/", '', $field);
                    if (in_array($field, $this->options)) {
                        if ($val != '!skip!' && $val) {
                            $result['options'][$field] = $val;
                        }
                    } else {
                        $result[$field] = $val;
                    }
                }
            }
        }
        return $result;
    }


    /**
     * @param array $data
     * @return array
     */
    public function afterPrepareData(array $data)
    {
        if (!isset($data['product_id'])) {
            $resource = $this->findResource($this->checkingField, $this->checkingValue, 'msProduct', $this->getSettings());
            if (!$resource) {
                $this->incrStatsRecord('errors');
                $err = $this->modx->lexicon('msimportexport_import_err_resource_nf', array('key' => $this->checkingField, 'value' => $this->checkingValue));
                $this->tools->log($err);
                return array();
            }
            $data['product_id'] = $resource->get('id');
        }
        return $data;
    }


    /**
     * @param array $data
     */
    public function work(array $data = array())
    {
        if (
            empty($data) ||
            !isset($data['remains']) ||
            (!isset($data['product_id']) && isset($data['id']))
        ) return;

        if (!empty($data['id'])) {
            $remain = $this->modx->getObject('msprRemains', $data['id']);
        } else {
            $remain = $this->modx->getObject('msprRemains', array(
                'product_id' => $data['product_id'],
                'options' => $this->modx->toJSON($data['options'])
            ));
        }

        if (is_object($remain)) {
            $this->action = 'update';
            $data['product_id'] = $remain->get('product_id');
        } else {
            $this->action = 'create';
            $remain = $this->modx->newObject('msprRemains');
        }

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
            $this->isRemoveRemains &&
            !$this->storage->hasKeyInStore('ids', $data['product_id'])
        ) {
            if ($this->debug) {
                $this->debug('Remove all remains' . print_r($data, 1));
            }
            $this->action = 'create';
            $remain = $this->modx->newObject('msprRemains');
            $this->removeProductAllRemains($data['product_id']);
        }

        if ($this->action == 'update') {
            $remain->set('remains', intval($data['remains']));
        } else {
            $remain->fromArray($data);
        }
        if (!$remain->save()) {
            $this->incrStatsRecord('errors');
            return;
        }
        $object = $remain->toArray();

        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }

        $this->incrStatsRecord($this->action . 'd');
        $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));
        $this->storage->pushStore('ids', $data['product_id'], $data['product_id']);
    }

    /**
     * @param int $productId
     * @param false $removeOptions
     * @return bool|void
     */
    public function removeProductAllRemains($productId, $removeOptions = false)
    {
        if ($items = $this->modx->getCollection('msprRemains', array('product_id' => $productId))) {
            foreach ($items as $item) {
                if ($removeOptions) {
                    if ($options = $item->get('options')) {
                        foreach ($options as $option) {
                            $this->removeProductOptions($productId, $option);
                        }
                    }
                }
                $item->remove();
            }
        }
        return true;
    }

    /**
     * @param int $productId
     * @param string $key
     * @return bool|void
     */
    public function removeProductOptions($productId, $key = '')
    {
        if ($productId) {
            $q = $this->modx->newQuery('msProductOption');
            $q->command('DELETE');
            $q->where(array(
                'product_id' => $productId,
            ));
            if (!empty($key)) {
                $q->where(array(
                    'key' => $key,
                ));
            }
            $q->prepare();
            return $q->stmt->execute();
        }
    }
}