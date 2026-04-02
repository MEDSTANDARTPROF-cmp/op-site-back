<?php

class IeMs2ProductImportWorker extends IeMs2CategoryImportWorker
{
    /** @var string $classKey */
    protected $classKey = 'msProduct';
    /** @var string $defaultFieldPrefix */
    protected $defaultFieldPrefix = 'product';
    /** @var array $productOptionMeta */
    protected $productOptionMeta = array();

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->processorsPath = MODX_CORE_PATH . 'components/iems2/processors/';
            $this->templateDefault = $this->modx->getOption('ms2_template_product_default', null, 0, true);
            $this->addPrepareFieldMethod('vendor', $this, 'prepareFieldVendor');
            $this->addPrepareFieldMethod('vendor_name', $this, 'prepareFieldVendor');
        }
        return $initialized;

    }

    /**
     * @param array $data
     */
    public function work(array $data = array())
    {
        if (empty($data)) {
            return;
        }

        if (
            !empty($this->getSetting('skip_action')) &&
            $this->getSetting('skip_action') == $this->action
        ) {
            return;
        }

        if (!isset($data['parent']) || $data['parent'] == '') {
            if (!$this->getSetting('skip_empty_parent', 1)) {
                $err = $this->modx->lexicon('msimportexport_import_err_parent_ns', array('info' => print_r($data, 1)));
                $this->tools->log($err);
            }
            return;
        }

        if (empty($data['pagetitle'])) {
            $err = $this->modx->lexicon('msimportexport_import_err_pagetitle_ns', array('info' => print_r($data, 1)));
            $this->tools->log($err);
            return;
        }

        $alias = $this->prepareAlias($data);
        if ($alias === false) {
            return;
        }

        $data['alias'] = $alias;

        if ($this->getSetting('create_article', 0) && empty($data['article'])) {
            $data['article'] = $this->makeArticle($data);
        }

        $response = $this->fireEvent('msieOnBeforeImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data));
        if (!is_array($response)) {
            if ($response === false) {
                $this->incrStatsRecord('errors');
            }
            return;
        }

        if ($response['skip']) {
            return;
        }

        $data = $response['data'];

        if (isset($data['keywords'])) {
            $_POST['keywords'] = $data['keywords'];
        }


        $data['_disable_map_generation'] = $this->disableMapGeneration;

        if ($this->debug) {
            $record = print_r($this->getReadRecord(), 1);
            $this->debug("Import before run processor.\n\naction: {$this->action}\nfile record: {$record}\nparams: " . print_r($data, 1));
        }

        /** @var modProcessorResponse $response */
        $this->modx->error->reset();
        $response = $this->runProcessor('mgr/product/' . $this->action, $data);
        if (!$response || $response->isError()) {
            $err = $this->modx->lexicon('msimportexport_import_err_import',
                array(
                    'action' => $this->action,
                    'message' => $response->getMessage(),
                    'info' => print_r($data, 1) . "\n" . print_r($response->getAllErrors(), 1)
                )
            );

            $this->tools->log($err);
            $this->incrStatsRecord('errors');

            if ($this->hasErrorDuplicationResource($response)) {
                $this->incrStatsRecord('duplication');
                $this->storage->pushStore('duplication', $this->reader->getOffset(), basename($this->file), true);
            }
            return;
        }

        $object = $response->getObject();

        if (!empty($data['categories'])) {
            if ($this->debug) {
                $this->debug("Adding product to show in categories: \n" . print_r($data['categories'], 1));
            }
            $this->addProductToSubCategories($object['id'], $data['categories'], $object['parent'], $this->getSettings());
        }


        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }

        $this->incrStatsRecord($this->action . 'd');
        $response = $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));

        if (!is_array($response)) {
            return;
        }

        $this->storage->pushStore('ids', $object['id'], $object['id']);
    }

    /**
     * @param string $field
     * @param mixed $val
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldVendor($field, $val, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = $this->getProductVendorId($val);
        return $result;
    }

    /**
     * @param array $data
     * @return string
     */
    public function makeArticle(array $data)
    {
        $article = microtime(true) * 10000;
        if ($templateArticle = $this->getSetting('template_article', 0)) {
            $data['msie_microtime'] = $article;
            if ($templateArticle = $this->tools->getPdoTools()->getChunk('@INLINE ' . $templateArticle, $data)) {
                $article = $templateArticle;
            }
        }
        return $article;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isFieldArrayType($field)
    {
        if (!$yes = parent::isFieldArrayType($field)) {
            if ($type = $this->getProductFieldType($field)) {
                if ($type == 'json' || $type == 'array') return true;
            } else if ($type = $this->getProductOptionFieldType($field)) {
                if ($type == 'combo-multiple' || $type == 'combo-options') return true;
            }
            $yes = false;
        }
        return $yes;
    }

    /**
     * @param string $field
     * @return null|string
     */
    public function getProductFieldType($field)
    {
        $meta = $this->modx->getFieldMeta('msProductData');
        if (isset($meta[$field])) return $meta[$field]['phptype'];
        return null;
    }

    /**
     * @param string $field
     * @return null|string
     */
    public function getProductOptionFieldType($field)
    {
        $field = str_replace('options-', '', $field);
        $meta = $this->getProductOptionMeta();
        if (isset($meta[$field])) return $meta[$field]['type'];
        return null;
    }

    /**
     * @return array
     */
    public function getProductOptionMeta()
    {
        $classKey = 'msOption';
        if (!$this->productOptionMeta) {
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey));
            if ($q->prepare() && $q->stmt->execute()) {
                while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->productOptionMeta[$item['key']] = $item;
                }
            }
        }
        return $this->productOptionMeta;
    }

    /**
     * @param int $productId
     * @param string $categories
     * @param int $parent
     * @param array $options
     * @return bool
     */
    public function addProductToSubCategories($productId, $categories, $parent = 0, array $options = array())
    {
        $firstDelimiter = $this->modx->getOption('first_delimiter', $options, '|', true);
        $secondDelimiter = $this->modx->getOption('second_delimiter', $options, '%', true);
        $categories = $this->tools->explodeAndClean($categories, $secondDelimiter);
        if (empty($categories)) {
            return false;
        }

        $this->removeProductFromSubCategories($productId, $parent);

        foreach ($categories as $category) {
            if (!is_numeric($category)) {
                $nesting = $this->tools->explodeAndClean($category, $firstDelimiter);
                if ($categoryId = $this->createResourceChain($nesting, null, $options)) {
                    $this->addProductToSubCategory($productId, $categoryId);
                }
            } else {
                $this->addProductToSubCategory($productId, $category);
            }
        }
        return true;
    }


    /**
     * @param string|array $resources
     * @param null|int $parent
     * @param array $options
     * @return int
     */
    public function createResourceChain($resources, $parent = null, $options = array())
    {
        $options['defaultFieldPrefix'] = 'resource';
        $options['defaultFieldTemplate'] = 'ms2_template_category_default';
        return parent::createResourceChain($resources, $parent, $options);
    }


    /**
     * @param int $productId
     * @param int $categoryId
     *
     * @return bool
     */
    public function addProductToSubCategory($productId, $categoryId)
    {
        if (!$o = $this->modx->getObject('msCategoryMember', array('category_id' => $categoryId, 'product_id' => $productId))) {
            $o = $this->modx->newObject('msCategoryMember');
            $o->set('product_id', $productId);
            $o->set('category_id', $categoryId);
            return $o->save();
        }
        return false;
    }


    /**
     * @param int $productId
     * @param int $parent
     */
    public function removeProductFromSubCategories($productId, $parent)
    {
        $q = $this->modx->newQuery('msCategoryMember');
        $q->where(array('product_id:=' => $productId, 'category_id:!=' => $parent));
        if ($c = $this->modx->getCollection('msCategoryMember', $q)) {
            foreach ($c as $item) {
                $item->remove();
            }
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function vendorIdExists($id)
    {
        $result = false;
        $classKey = 'msVendor';

        if ($this->storage->hasKeyInStore('vendor_id_exists', $id)) {
            return true;
        }

        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id')));
        $q->where(array('id' => $id));
        if ($q->prepare() && $q->stmt->execute()) {
            if ($q->stmt->fetch(PDO::FETCH_COLUMN)) {
                $this->storage->pushStore('vendor_id_exists', $id, $id);
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param int|string $vendor
     * @param bool $create
     * @return int
     */
    public function getProductVendorId($vendor, $create = true)
    {
        if (is_numeric($vendor)) {
            if (empty($vendor) || !$this->vendorIdExists($vendor)) {
                $vendor = 0;
            }
            return $vendor;
        } else {
            $vendor = trim($vendor);
            if (!$id = $this->storage->getStoreVal('vendor_id', $vendor, 0)) {
                if (!$id = $this->getProductVendorIdByName($vendor)) {
                    if ($create) {
                        $id = $this->addProductVendor($vendor);
                    }
                }
                if ($id) {
                    $this->storage->pushStore('vendor_id', $id, $vendor);
                }
            }
        }
        return $id;
    }

    /**
     * @param string $vendor
     * @return int
     */
    public function getProductVendorIdByName($vendor)
    {
        $id = 0;
        $q = $this->modx->newQuery('msVendor');
        $q->select(array('id'));
        $q->where(array('name:=' => $vendor));
        if ($q->prepare() && $q->stmt->execute()) {
            $id = $q->stmt->fetchColumn();
        }
        return $id;
    }

    /**
     * @param string $vendor
     * @return int
     */
    public function addProductVendor($vendor)
    {
        $id = 0;
        $msVendor = $this->modx->newObject('msVendor');
        $msVendor->set('name', $vendor);
        if ($msVendor->save()) {
            $id = $msVendor->get('id');
        }
        return $id;
    }

}