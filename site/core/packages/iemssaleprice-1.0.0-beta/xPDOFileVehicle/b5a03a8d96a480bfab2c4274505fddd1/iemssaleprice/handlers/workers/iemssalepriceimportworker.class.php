<?php

class ieMsSalePriceImportWorker extends MsIeImportWorker
{
    /** @var bool $isRemovePrices */
    protected $isRemovePrices = false;

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->removePrefixField = 'mssp_';
            $this->stats = array('errors' => 0, 'created' => 0, 'updated' => 0);
            $this->isRemovePrices = $this->getSetting('mssp_remove_prices', false);
            $this->checkingField = $this->getSetting('checking_field', '');
            if ($this->checkingField) {
                $this->addPrepareFieldMethod($this->checkingField, $this, 'prepareFieldCheckingValue');
            }
            $this->addPrepareFieldMethod('mssp_type', $this, 'prepareFieldType');
        }
        return $initialized;
    }

    /**
     * @param array $data
     * @return array
     */
    public function afterPrepareData(array $data)
    {
        if (!isset($data['rid'])) {
            $resource = $this->findResource($this->checkingField, $this->checkingValue, 'msProduct', $this->getSettings());
            if (!$resource) {
                $this->incrStatsRecord('errors');
                $err = $this->modx->lexicon('msimportexport_import_err_resource_nf', array('key' => $this->checkingField, 'value' => $this->checkingValue));
                $this->tools->log($err);
                return array();
            }
            $data['rid'] = $resource->get('id');
        }
        return $data;
    }

    /**
     * @param array $data
     */
    public function work(array $data = array())
    {
        if (empty($data) || empty($data['rid'])) return;
        $this->action = 'create';
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
            $this->isRemovePrices &&
            !$this->storage->hasKeyInStore('ids', $data['rid'])
        ) {
            if ($this->debug) {
                $this->debug('Remove all prices' . print_r($data, 1));
            }
            $this->removeProductAllSalePrice($data['rid']);
        }

        /** @var msspPrice $saleprice */
        $saleprice = null;
        if (isset($data['count'])) {
            $saleprice = $this->modx->getObject('msspPrice', array('rid' => $data['rid'], 'count' => $data['count']));
        }

        if ($saleprice) {
            $this->action = 'update';
        } else {
            $saleprice = $this->modx->newObject('msspPrice');
        }

        $saleprice->fromArray($data);
        if ($saleprice->save()) {
            $object = $saleprice->toArray();
        } else {
            $this->incrStatsRecord('errors');
            return;
        }

        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }

        $this->incrStatsRecord($this->action . 'd');
        $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));
        $this->storage->pushStore('ids', $data['rid'], $data['rid']);
    }

    /**
     * @param int $productId
     * @return bool|void
     */
    public function removeProductAllSalePrice($productId)
    {
        if ($items = $this->modx->getCollection('msspPrice', array('rid' => $productId))) {
            foreach ($items as $item) {
                $item->remove();
            }
        }
        return true;
    }

    /**
     * @param string $field
     * @param mixed $val
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldType($field, $val, array $data, array $result, MsIeWorker &$worker)
    {
        if (!is_numeric($val)) {
            switch ($val) {
                case '=':
                    $val = 1;
                    break;
                case '+':
                    $val = 2;
                    break;
                case '-':
                    $val = 3;
                    break;
                case '*':
                    $val = 4;
                    break;
                case '/':
                    $val = 5;
                    break;
                case '%':
                    $val = 6;
                    break;
            }
        }
        $result['type'] = $val;
        return $result;
    }
}