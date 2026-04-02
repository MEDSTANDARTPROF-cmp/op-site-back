<?php

class ieMsOptionsColorImportWorker extends MsIeImportWorker
{
    /** @var bool $isRemoveColor */
    protected $isRemoveColor = false;
    /** @var array $recordFindFields */
    protected $recordFindFields = array();

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->removePrefixField = 'msoc_';
            $this->stats = array('errors' => 0, 'created' => 0, 'updated' => 0);
            $this->isRemoveColor = $this->getSetting('msoc_remove_color', false);
            $this->checkingField = $this->getSetting('checking_field', '');
            $this->recordFindFields = $this->getSetting('msoc_record_find_fields', 'key');
            $this->recordFindFields = $this->tools->explodeAndClean($this->recordFindFields);
            if ($this->checkingField) {
                $this->addPrepareFieldMethod($this->checkingField, $this, 'prepareFieldCheckingValue');
            }
        }
        return $initialized;
    }


    public function beforeStart()
    {
        parent::beforeStart();
        $isDisableColor = $this->getSetting('msoc_disable_color', false);
        if ($isDisableColor) {
            $this->disableAllProductsColors();
        }
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
        if (!isset($data['active'])) {
            $data['active'] = 1;
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
        $criteria = array('rid' => $data['rid']);

        foreach ($this->recordFindFields as $key) {
            if (isset($data[$key]))
                $criteria[$key] = $data[$key];
        }

        if ($color = $this->modx->getObject('msocColor', $criteria)) {
            $this->action = 'update';
        } else {
            $color = $this->modx->newObject('msocColor');
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
            $this->isRemoveColor &&
            !$this->storage->hasKeyInStore('ids', $data['rid'])
        ) {
            if ($this->debug) {
                $this->debug('Remove all colors' . print_r($data, 1));
            }
            $this->removeProductAllColor($data['rid']);
        }

        $this->tools->getMsOptionsColorInstance()->setProductOptions($data['rid'], array($data['key'] => $data['value']), $data['key']);
        $color->fromArray($data, '', true);

        if (!$color->save()) {
            $this->incrStatsRecord('errors');
            return;
        }

        $object = $color->toArray();

        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }
        $this->incrStatsRecord($this->action . 'd');
        $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));
        $this->storage->pushStore('ids', $data['rid'], $data['rid']);
    }


    public function disableAllProductsColors()
    {
        $table = $this->modx->getTableName('msocColor');
        $update = $this->modx->prepare("UPDATE {$table} SET active = 0");
        $update->execute(array());
    }

    /**
     * @param int $productId
     * @return bool|void
     */
    public function removeProductAllColor($productId)
    {
        if ($items = $this->modx->getCollection('msocColor', array('rid' => $productId))) {
            foreach ($items as $item) {
                $item->remove();
            }
        }
        return true;
    }

}