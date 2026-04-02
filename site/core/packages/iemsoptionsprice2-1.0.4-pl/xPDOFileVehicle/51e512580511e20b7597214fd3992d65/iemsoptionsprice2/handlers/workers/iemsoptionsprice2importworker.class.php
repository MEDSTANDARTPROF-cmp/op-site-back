<?php

class ieMsOptionsPrice2ImportWorker extends MsIeImportWorker
{
    /** @var bool $isRemoveModification */
    protected $isRemoveModification = false;
    /** @var array $keys */
    protected $keys = array();
    /** @var msoptionsprice $msoptionsprice */
    protected $msoptionsprice;

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {

            $this->msoptionsprice = $this->modx->getService('msoptionsprice');
            $this->removePrefixField = 'msopm_';
            $this->stats = array('errors' => 0, 'created' => 0, 'updated' => 0);
            $this->isRemoveModification = $this->getSetting('msopm_remove_modification', false);
            $this->checkingField = $this->getSetting('checking_field', '');
            $this->keys = array_keys($this->modx->getFields('msopModification'));
            if ($this->checkingField) {
                $this->addPrepareFieldMethod($this->checkingField, $this, 'prepareFieldCheckingValue');
            }
        }
        return $initialized;
    }


    public function beforeStart()
    {
        parent::beforeStart();
        $isDisableModification = $this->getSetting('msopm_disable_modification', false);
        if ($isDisableModification) {
            $this->disableAllProductsModifications();
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $result = array('options' => array());
        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(), 1));
        }
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
                    if (in_array($field, $this->keys)) {
                        $result[$field] = $val;
                    } else {
                        if ($val != '!skip!' && $val) {
                            $result['options'][$field] = $val;
                        }
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
        if (
            !empty($data['image']) &&
            !empty($data['rid']) &&
            !is_numeric($data['image'])
        ) {
            $data['image'] = $this->findProductImageIdByFile($data['rid'], $data['image']);
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
        if (empty($data['options']) && isset($data['article'])) {
            $this->action = 'update';
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
            $this->isRemoveModification &&
            !$this->storage->hasKeyInStore('ids', $data['rid'])
        ) {
            if ($this->debug) {
                $this->debug('Remove all modifications' . print_r($data, 1));
            }
            $this->removeProductModification($data['rid']);
        }
        if ($this->action == 'update') {
            $object = $this->updateModification($data);
        } else {
            $object = $this->saveProductModification($data['rid'], $data);
        }
        if (!$object) {
            $this->incrStatsRecord('errors');
            return;
        }
        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }
        $this->incrStatsRecord($this->action . 'd');
        $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));
        $this->storage->pushStore('ids', $data['rid'], $data['rid']);
        unset($data, $object);
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterOptionsPrice2Data(array $data)
    {
        $result = array('options' => array());
        if (empty($data)) return $result;
        $keys = array_keys($this->modx->getFields('msopModification'));
        foreach ($data as $key => $val) {
            if (preg_match('/^msopm_\w+$/', $key)) {
                $field = str_replace('msopm_', '', $key);
                if (in_array($field, $keys)) {
                    $result[$field] = $val;
                } else {
                    if ($val != '!skip!') {
                        $result['options'][$field] = $val;
                    }
                }
            }
        }
        return $result;
    }


    /**
     * @param int $productId
     * @param string $fileName
     * @return int
     */
    public function findProductImageIdByFile($productId, $fileName)
    {
        $classGallery = trim($this->modx->getOption('msoptionsprice_modification_gallery_class', null, 'msProductFile', true));
        if ($classGallery) {
            $imageName = pathinfo($fileName, PATHINFO_BASENAME);
            $q = $this->modx->newQuery($classGallery);
            $orImageName = str_replace('_', '-', $imageName);
            $q->where(array(
                '`' . $classGallery . '`.parent`:=' => 0,
            ));
            $q->where(array(
                '`' . $classGallery . '`.`file`:=' => $imageName,
                'OR:`' . $classGallery . '`.`file`:=' => $orImageName,
            ));
            $q->select('id');
            switch ($classGallery) {
                case 'msProductFile':
                    $q->where(array('product_id' => $productId));
                    break;
                case 'msResourceFile':
                    $q->where(array('resource_id' => $productId));
                    break;
            }

            if ($q->prepare() && $q->stmt->execute()) {
                return $q->stmt->fetch(PDO::FETCH_COLUMN);
            }
        }
        return 0;
    }

    public function disableAllProductsModifications()
    {
        $table = $this->modx->getTableName('msopModification');
        $update = $this->modx->prepare("UPDATE {$table} SET active = 0");
        $update->execute(array());
    }

    /**
     * @param array $data
     * @return array
     */
    protected function updateModification(array $data = array())
    {
        $result = array();
        if (empty($data)) return $result;
        if (!$modification = $this->modx->getObject('msopModification', array('rid' => $data['rid'], 'article' => $data['article']))) {
            $modification = $this->modx->newObject('msopModification');
        }
        $modification->fromArray($data);
        if ($modification->save()) {
            $result = $modification->toArray();
        }
        return $result;
    }

    /**
     * @param int $rid
     * @param array $data
     * @param bool $setProductOptions
     *
     * @return void
     */
    protected function saveProductModification(int $rid, array $data, bool $setProductOptions = true)
    {
        $modification = null;
        $classModification = 'msopModification';
        $options = $data['options'] ?? array();
        if (!empty($options)) {
            unset($data['id'], $data['rank'], $data['options']);
            $data['rid'] = $rid;
            if (!$this->isRemoveModification) {
                $modificationData = $this->msoptionsprice->getModificationByOptions($rid, $options, true, array(0), array(0), null);
                if ($modificationData) {
                    /** @var msopModification $modification */
                    $modification = $this->modx->getObject($classModification, array('id' => (int)$modificationData['id']));
                }
            }
            if (!$modification) {
                $modification = $this->modx->newObject($classModification);
            }

            $modification->fromArray($data, '', true, true);
            if ($modification->save()) {
                if ($setProductOptions) {
                    $this->msoptionsprice->setProductOptions($rid, $options);
                }
                if (!$this->isRemoveModification) {
                    $this->removeModificationOptions($modification->get('id'), $rid);
                }
                $this->saveModificationOptions($modification->get('id'), $rid, $options);
            }
        } else if ($key = $this->msoptionsprice->getOption('get_modification_by', null, 'name')) {
            unset($data['id'], $data['rank'], $data['options']);
            $data['rid'] = $rid;


            $q = $this->modx->newQuery($classModification);
            $q->where(array(
                'rid' => $rid,
                $key => $row[$key] ?? null,
            ));

            if (!$modification = $this->modx->getObject($classModification, $q)) {
                $modification = $this->modx->newObject($classModification);
            }
            $modification->fromArray($data, '', true, true);
            if ($modification->save()) {
                $this->removeModificationOptions($modification->get('id'), $rid);
            }
        }
        unset($data, $modification);
        return $this->getProductModification($rid);
    }

    /**
     * @param int $rid
     * @param array $data
     * @param bool $setProductOptions
     *
     * @return void
     */
    protected function saveProductModification_(int $rid, array $data, bool $setProductOptions = true)
    {
        $classModification = 'msopModification';
        $options = $data['options'] ?? array();
        if (!empty($options)) {
            unset($data['id'], $data['rank'], $data['options']);
            $data['rid'] = $rid;
            $modificationData = $this->msoptionsprice->getModificationByOptions($rid, $options, true, array(0), array(0), null);
            if ($modificationData) {
                /** @var msopModification $modification */
                $modification = $this->modx->getObject($classModification, array('id' => (int)$modificationData['id']));
            }
            if (!$modification) {
                $modification = $this->modx->newObject($classModification);
            }

            $modification->fromArray($data, '', true, true);
            if ($modification->save()) {
                if ($setProductOptions) {
                    $this->msoptionsprice->setProductOptions($rid, $options);
                }
                $this->removeModificationOptions($modification->get('id'), $rid);
                $this->saveModificationOptions($modification->get('id'), $rid, $options);
            }
        } else if ($key = $this->msoptionsprice->getOption('get_modification_by', null, 'name')) {
            unset($data['id'], $data['rank'], $data['options']);
            $data['rid'] = $rid;


            $q = $this->modx->newQuery($classModification);
            $q->where(array(
                'rid' => $rid,
                $key => $row[$key] ?? null,
            ));

            if (!$modification = $this->modx->getObject($classModification, $q)) {
                $modification = $this->modx->newObject($classModification);
            }
            $modification->fromArray($data, '', true, true);
            if ($modification->save()) {
                $this->removeModificationOptions($modification->get('id'), $rid);
            }
        }
        unset($data);
        return $this->getProductModification($rid);
    }

    /**
     * @param int $rid
     * @param bool $withOptions
     *
     * @return array
     */
    protected function getProductModification(int $rid = 0, bool $withOptions = true)
    {
        $modifications = array();
        $classModification = 'msopModification';
        $q = $this->modx->newQuery($classModification);
        $q->select($this->modx->getSelectColumns($classModification, $classModification, '', array(), true));
        $q->sortby("rank", "ASC");
        $q->where(array(
            "{$classModification}.rid" => "{$rid}",
        ));

        if ($q->prepare() and $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($withOptions) {
                    $row['options'] = $this->getModificationOptions($row['id'], $rid);
                }
                $modifications[] = $row;
            }
        }
        return $modifications;
    }

    /**
     * @param int $rid
     * @param array $modifications
     * @param bool $removeProductOptions
     *
     * @return true
     */
    protected function removeProductModification(int $rid = 0, array $modifications = array(0), bool $removeProductOptions = true)
    {
        $classModification = 'msopModification';

        foreach ($modifications as $row) {
            $where = array(
                'rid' => $rid,
            );

            if (isset($row['name'])) {
                $where['name'] = $row['name'];
            }
            if (isset($row['type'])) {
                $where['type'] = $row['type'];
            }
            if (isset($row['price'])) {
                $where['price'] = $row['price'];
            }
            if (isset($row['article'])) {
                $where['article'] = $row['article'];
            }
            $q = $this->modx->newQuery($classModification);
            $q->where($where);

            /** @var msopModification $modification */
            if ($objects = $this->modx->getIterator($classModification, $q)) {
                foreach ($objects as $modification) {
                    $options = $this->getModificationOptions($modification->get('id'), $rid);
                    if ($modification->remove()) {
                        if ($removeProductOptions) {
                            $this->msoptionsprice->removeProductOptions($rid, $options);
                        }
                        $this->removeModificationOptions($modification->get('id'), $rid);
                    }
                }
            }
        }
        return true;
    }


    /**
     * @param int $mid
     * @param int $rid
     * @param string $key
     * @param bool $process
     * @param string $prefix
     *
     * @return array
     */
    protected function getModificationOptions(int $mid, int $rid, string $key = '', bool $process = false, string $prefix = '')
    {
        $options = array();
        $q = $this->modx->newQuery('msopModificationOption');
        $q->leftJoin('msOption', 'msOption', 'msopModificationOption.key = msOption.key');
        $q->groupby('msopModificationOption.key');
        if ($process) {
            $q->select($this->modx->getSelectColumns('msopModificationOption', 'msopModificationOption'));
            $q->select($this->modx->getSelectColumns('msOption', 'msOption', '', array('caption', 'description', 'measure_unit'), false));
        } else {
            $q->select($this->modx->getSelectColumns('msopModificationOption', 'msopModificationOption', '', array('key', 'value'), false));
            $q->select($this->modx->getSelectColumns('msOption', 'msOption', '', array('caption'), false));
        }
        if ($mid) {
            $q->where(array(
                "msopModificationOption.mid" => "{$mid}",
            ));
        }
        if ($rid) {
            $q->where(array(
                "msopModificationOption.rid" => "{$rid}",
            ));

            $q->innerJoin('msProduct', 'msProduct', 'msProduct.id = ' . $rid);
            $q->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id = msOption.id AND msCategoryOption.category_id = msProduct.parent');

            $q->sortby('msCategoryOption.rank', 'ASC');
            $q->select('msCategoryOption.rank');
        }

        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $k = $prefix . $row['key'];
                if (isset($options[$k])) {
                    if (!is_array($options[$k])) {
                        $options[$k] = array($options[$k]);
                    }
                    $options[$k][] = $row['value'];
                } else {
                    $options[$k] = $row['value'];
                }
                if ($process) {
                    foreach ($row as $x => $value) {
                        $options[$k . '.' . $x] = $value;
                    }
                }
            }
        }
        if ($key and !$process) {
            $options = $options[$key] ?? '';
        }

        return $options;
    }

    /**
     * @param int $mid
     * @param int $rid
     * @param string $key
     *
     * @return void
     */
    protected function removeModificationOptions(int $mid = 0, int $rid = 0, string $key = '')
    {
        $table = $this->modx->getTableName('msopModificationOption');
        if (empty($key)) {
            $sql = "DELETE FROM {$table} WHERE `mid` = '{$mid}' AND `rid` = '{$rid}';";
        } else {
            $sql = "DELETE FROM {$table} WHERE `mid` = '{$mid}' AND `rid` = '{$rid}' AND `key` = '{$key}';";
        }

        $stmt = $this->modx->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * @param int $mid
     * @param int $rid
     * @param array $options
     *
     * @return void
     */
    protected function saveModificationOptions(int $mid = 0, int $rid = 0, array $options = array())
    {
        $table = $this->modx->getTableName('msopModificationOption');

        $sql = "INSERT INTO {$table} (`mid`, `rid`, `key`, `value`) VALUES (:mid, :rid, :key, :value);";
        $stmt = $this->modx->prepare($sql);
        foreach ($options as $key => $field) {
            if (empty($key)) {
                continue;
            }
            if (!is_array($field)) {
                $field = array($field);
            }
            foreach ($field as $value) {
                $stmt->bindValue(':mid', $mid);
                $stmt->bindValue(':rid', $rid);
                $stmt->bindValue(':key', $key);
                $stmt->bindValue(':value', $value);
                $stmt->execute();
            }
        }
        $stmt->closeCursor();
    }


}