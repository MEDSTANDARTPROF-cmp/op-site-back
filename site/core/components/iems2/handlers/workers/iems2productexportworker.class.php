<?php

class IeMs2ProductExportWorker extends MsIeResourceExportWorker
{
    use IeMs2ProductExportWorkerTrait;

    /** @var string $classKey */
    protected $classKey = 'msProduct';
    /** @var bool $hasOptions */
    protected $hasOptions = false;
    /** @var bool $allowPriceModification */
    protected $allowPriceModification = false;

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->hasOptions = $this->hasOptionsFields();
            $this->addPrepareFieldMethod('categories', $this, 'prepareFieldCategories');
            $this->addPrepareFieldMethod('url_thumb', $this, 'prepareFieldUrlThumb');
            $this->addPrepareFieldMethod('url_image', $this, 'prepareFieldUrlImage');
            $this->addPrepareFieldMethod('price', $this, 'prepareFieldPrice');
            $this->addPrepareFieldMethod('old_price', $this, 'prepareFieldPrice');
            $this->allowPriceModification = $this->getSetting('allow_price_modification', false);
            $this->addLanguageTopic('minishop2:product');
        }
        return $initialized;
    }

    /**
     * @return array
     */
    public function buildQueryConfig()
    {

        $config = parent::buildQueryConfig();
        $config['innerJoin']['Data'] = array('class' => 'msProductData', 'alias' => 'Data', 'on' => '`' . $this->classKey . '`.`id` = `Data`.`id`');
        $config['leftJoin']['Vendor'] = array('class' => 'msVendor', 'alias' => 'Vendor', 'on' => '`Data`.`vendor` = `Vendor`.`id`');

        $config['select']['Data'] = $this->modx->getSelectColumns('msProductData', 'Data', '', array('id'), true);
        $config['select']['Vendor'] = $this->modx->getSelectColumns('msVendor', 'Vendor', 'vendor_');

        if ($this->getVendorIds()) {
            $config['where']["`Data`.`vendor`:IN"] = $this->getVendorIds();
        }

        return $config;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareFieldKeys(array $data)
    {
        if ($data = parent::prepareFieldKeys($data)) {
            $options = $this->tools->getProductOptionsFields('', '');
            foreach ($data as &$field) {
                if (isset($options[$field])) {
                    $field = 'options-' . $field;
                } else {
                    switch ($field) {
                        case 'vendor_name':
                            $field = 'vendor';
                            break;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function beforePrepareData(array $data)
    {
        if (!$this->hasOptions) return $data;
        $options = $this->modx->call('msProductData', 'loadOptions', array(&$this->modx, $data['id']));
        return array_merge($data, $options);
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldCategories($field, array $data, array $result, MsIeWorker &$worker)
    {
        $categories = '';
        $firstDelimiter = $this->getSetting('first_delimiter', '|');
        $secondDelimiter = $this->getSetting('second_delimiter', '%');
        if ($subCategories = $this->getProductSubCategories($data['id'])) {
            foreach ($subCategories as $id => $pagetitle) {
                if ($categories) $categories .= $secondDelimiter;
                if ($this->getSetting('multicategory_format', 0)) {
                    $categories .= $id;
                } else {
                    $categories .= $this->getResourceTitleParents($id, $firstDelimiter);
                    $categories .= $firstDelimiter . $pagetitle;
                }
            }
        }
        $result[$field] = $categories;
        return $result;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldUrlThumb($field, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = $this->tools->getSiteUrl() . $data['thumb'];
        return $result;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldUrlImage($field, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = $this->tools->getSiteUrl() . $data['image'];
        return $result;
    }


    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldPrice($field, array $data = array(), array $result, MsIeWorker &$worker)
    {
        if (isset($result[$field])) {
            return $result;
        }

        if ($this->allowPriceModification) {
            $q = $this->modx->newQuery('modPluginEvent', array('event:IN' => array('msOnGetProductPrice', 'msOnGetProductWeight')));
            $q->innerJoin('modPlugin', 'modPlugin', 'modPlugin.id = modPluginEvent.pluginid');
            $q->where('modPlugin.disabled = 0');
            $modificators = $this->modx->getOption('ms2_price_snippet', null, false, true);
            if (
                $modificators ||
                $this->modx->getOption('ms2_weight_snippet', null, false, true) ||
                $this->modx->getCount('modPluginEvent', $q)
            ) {
                /* @var msProductData $product */
                $product = $this->modx->newObject('msProductData');
                $product->fromArray($data, '', true, true);
                $result['weight'] = $product->getWeight($data);
                $tmp = $data['price'];
                $result['price'] = $product->getPrice($data);
                if ($result['price'] != $tmp) {
                    $result['old_price'] = $this->formatPrice($tmp);
                }
                $result['price'] = $this->formatPrice($result['price']);
                return $result;
            }
        }
        $result[$field] = $this->formatPrice($data[$field]);
        return $result;
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getProductSubCategories($productId)
    {
        $result = array();
        $classKey = 'msCategoryMember';
        $q = $this->modx->newQuery($classKey);
        $q->rightJoin('msCategory', 'Category', array('Category.id = msCategoryMember.category_id'));
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('category_id')));
        $q->select('Category.pagetitle');
        $q->where(array('product_id' => $productId));

        if ($q->prepare() && $q->stmt->execute()) {
            while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$item['category_id']] = $item['pagetitle'];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFieldNames()
    {
        $result = array();
        if ($keys = $this->getFieldKeys()) {
            foreach ($keys as $key) {
                if (isset($this->lexicon[$key])) {
                    $result[] = $this->lexicon[$key];
                } else {
                    if ($this->isTvField($key)) {
                        $tvName = preg_replace('/^(tv\.)(\w+)$/', '$2', $key);
                        $tvCaption = $this->getTvCaption($tvName);
                        if ($tvCaption != $tvName) {
                            $result[] = $tvCaption;
                            continue;
                        }
                    } else if ($optionCaption = $this->getProductOptionCaption($key)) {
                        $result[] = $optionCaption;
                        continue;
                    } else if ($this->modx->lexicon->exists('msie_alias_' . $key)) {
                        $result[] = $this->modx->lexicon('msie_alias_' . $key);
                        continue;
                    } else if ($this->modx->lexicon->exists($key)) {
                        $result[] = $this->modx->lexicon($key);
                        continue;
                    }
                    $result[] = $key;
                }
            }
        }
        return $result;
    }

}

trait  IeMs2ProductExportWorkerTrait
{
    /** @var null null|array $vendorIds */
    protected $vendorIds = null;
    /** @var array $priceFormat */
    protected $priceFormat;
    /** @var array|null */
    protected $productOptions = null;

    /**
     * Function for price format
     *
     * @param $price
     *
     * @return int|mixed|string
     */
    public function formatPrice($price = 0)
    {
        if (!$this->priceFormat) {
            $this->priceFormat = $this->tools->fromJSON($this->getSetting('price_format', '[2, ".", ""]'), array(2, '.', ''));
        }
        $price = number_format($price, $this->priceFormat[0], $this->priceFormat[1], $this->priceFormat[2]);
        if ($this->getSetting('price_format_no_zeros', true)) {
            $tmp = explode($this->priceFormat[1], $price);
            $tmp[1] = rtrim(rtrim(@$tmp[1], '0'), '.');
            $price = !empty($tmp[1]) ? $tmp[0] . $this->priceFormat[1] . $tmp[1] : $tmp[0];
        }
        return $price;
    }

    /**
     * @param string $option
     * @return string
     */
    public function getProductOptionCaption($option)
    {
        $options = $this->getProductOptions();
        if (isset($options[$option])) {
            return empty($options[$option]['alias']) ? $option : $options[$option]['alias'];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getProductOptions()
    {
        if ($this->productOptions === null) {
            $this->productOptions = $this->tools->getProductOptionsFields('', '');
        }
        return $this->productOptions;
    }

    /**
     * @return bool
     */
    public function hasOptionsFields()
    {
        $options = $this->getProductOptions();
        $fields = $this->getFields();
        if (empty($options) || empty($fields)) return false;
        foreach ($fields as $field) {
            if (isset($options[$field])) return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getVendorIds()
    {
        if ($this->vendorIds === null) {
            $vendorIds = array();
            if ($ids = $this->getSetting('vendors')) {
                $vendorIds = $this->tools->explodeAndClean($ids);
            }
            $this->vendorIds = $vendorIds;
        }
        return $this->vendorIds;
    }

    /**
     * @return array
     */
    public function getResourceIds()
    {
        if ($this->resourceIds === null) {
            $resources = array();
            if ($ids = $this->getSetting('resources')) {
                $ctx = $this->getSetting('ctx', 'web');
                $depth = $this->getSetting('search_depth', 10);
                $ids = $this->tools->explodeAndClean($ids);
                foreach ($ids as $id) {
                    $resources[] = (int)$id;
                    $childIds = $this->modx->getChildIds($id, $depth, array('context' => $ctx));
                    $resources = array_merge($resources, $childIds);
                }
                $q = $this->modx->newQuery('msCategoryMember');
                $q->where(array('category_id:IN' => $resources));
                $q->select('product_id');
                if ($q->prepare() && $q->stmt->execute()) {
                    $members = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
                    $resources = array_merge($resources, $members);
                }
            }
            $this->resourceIds = $resources;
        }
        return $this->resourceIds;
    }

    /**
     * @param int $id
     * @param int $depth
     * @param array $options
     * @return array
     */
    public function getCategoryParentIds($id, $depth = 10, array $options = array())
    {
        $ids = $this->modx->getParentIds($id, $depth, $options);
        if ($ids) {
            $classKey = 'msCategory';
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id')));
            $q->where(array(
                'id:IN' => $ids,
                'class_key' => 'msCategory',
            ));
            if ($q->prepare() && $q->stmt->execute()) {
                $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        }
        return $ids;
    }

    /**
     * @param int $id
     * @param int $depth
     * @param array $options
     * @return array
     */
    public function getCategoryChildIds($id, $depth = 10, array $options = array())
    {
        $ids = $this->modx->getChildIds($id, $depth, $options);
        if ($ids) {
            $classKey = 'msCategory';
            $q = $this->modx->newQuery($classKey);
            $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id')));
            $q->where(array(
                'id:IN' => $ids,
                'class_key' => 'msCategory',
            ));
            if ($q->prepare() && $q->stmt->execute()) {
                $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        }
        return $ids;
    }


    /**
     * @param string[] $columns
     * @return array
     */
    public function getCategories($columns = array())
    {
        $ids = array();
        $result = array();
        $columns = $columns ? $columns : array('id', 'parent', 'pagetitle');
        $classKey = 'msCategory';
        if ($resources = $this->getSetting('resources')) {
            $ctx = $this->getSetting('ctx', 'web');
            $depth = $this->getSetting('search_depth', 10);
            $resources = $this->tools->explodeAndClean($resources);
            foreach ($resources as $id) {
                $ids[] = $id;
                $childIds = $this->getCategoryChildIds($id, $depth, array('context' => $ctx));
                if ($childIds) {
                    $ids = array_merge_recursive($ids, $childIds);
                }
            }
        }

        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', $columns));
        $q->where(array(
            'class_key' => 'msCategory',
            'deleted' => 0
        ));

        if (!empty($ids)) {
            $q->where(array(
                'id:IN' => $ids
            ));
        }

        $q->sortby('parent,id', 'ASC');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($item = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $result[$item['id']] = $item;
            }
            if (!empty($resources) && !empty($result)) {
                foreach ($resources as $id) {
                    if (isset($result[$id])) {
                        $parent = $result[$id]['parent'];
                        if ($parent && empty($result[$parent])) {
                            $result[$id]['parent'] = 0;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string[] $columns
     * @return array
     */
    public function getCategories2($columns = array())
    {
        $ids = array();
        $result = array();
        $columns = $columns ? $columns : array('id', 'parent', 'pagetitle');
        $classKey = 'msCategory';
        if ($resources = $this->getSetting('resources')) {
            $ctx = $this->getSetting('ctx', 'web');
            $depth = $this->getSetting('search_depth', 10);
            $resources = $this->tools->explodeAndClean($resources);
            foreach ($resources as $id) {
                $ids[] = $id;
                $parentIds = $this->getCategoryParentIds($id, $depth, array('context' => $ctx));
                if ($parentIds) {
                    $ids = array_merge_recursive($ids, $parentIds);
                }
            }
        }

        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', $columns));
        $q->where(array(
            'class_key' => 'msCategory',
            'deleted' => 0
        ));

        if (!empty($ids)) {
            $q->where(array(
                'id:IN' => $ids
            ));
        }

        $q->sortby('parent,id', 'ASC');
        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;
    }
}