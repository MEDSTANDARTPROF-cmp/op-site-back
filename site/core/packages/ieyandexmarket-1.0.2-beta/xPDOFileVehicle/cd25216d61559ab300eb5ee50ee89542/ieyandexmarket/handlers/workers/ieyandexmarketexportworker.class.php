<?php
include_once MODX_CORE_PATH . 'components/iems2/handlers/workers/iems2productexportworker.class.php';

class IeYandexMarketExportWorker extends IeMs2ProductExportWorker
{
    /** @var IeGallery */
    public $iegallery;
    /** @var IeMsSalePrice */
    public $iemssaleprice;
    /** @var  bool */
    protected $includeOptionsPrice2;
    /** @var bool */
    protected $onlySalePricePrice;
    /** @var array $multicategories */
    protected $multicategories = array();

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        $this->isConverValueArr2Str = false;
        if ($initialized === true) {
            $this->iegallery = $this->modx->getService('iegallery', 'IeGallery');
            if ($this->iegallery) {
                if (!$this->getSetting('gallery_type')) {
                    $this->setSetting('gallery_type', 'minishop2');
                }
                $this->decodeJsonSetting(
                    'ym_param',
                    'ym_delivery_options',
                    'ym_pickup_options'
                );

                $this->explodeAndCleanSetting(',', 'ym_currencies', 'ym_description_fields', 'ym_exclude_saleprice_price');
                $this->setSetting('ym_site_url', $this->tools->getSiteUrl());
                $this->setupFields();
                $this->hasOptions = $this->hasOptionsFields();
                $this->addPrepareFieldMethod('images', $this, 'prepareFieldImages');
                $this->onlySalePricePrice = $this->getSetting('ym_only_saleprice_price', false);
                $this->includeOptionsPrice2 = $this->getSetting('ym_include_optionsprice2');

                if ($this->getSetting('ym_multicategories', false)) {
                    $this->multicategories = $this->tools->getMulticategories();
                }
                if ($this->includeOptionsPrice2) {
                    if (!$this->tools->hasAddition('msoptionsprice')) {
                        $this->includeOptionsPrice2 = false;
                    }
                }
                if ($this->getSetting('ym_include_saleprice')) {
                    if (
                        $this->tools->hasAddition('mssaleprice') &&
                        $this->tools->hasAddition('iemssaleprice')

                    ) {
                        $this->iemssaleprice = $this->modx->getService('iemssaleprice', 'IeMsSalePrice');
                        $this->iemssaleprice->getTools(array('json_response' => false));
                    }
                }

            } else {
                $initialized = $this->modx->lexicon('ieyandexmarket_err_get_service_iegallery');
            }
        }
        return $initialized;
    }

    public function setupFields()
    {

        $desc = $this->getSetting('ym_description_fields');
        $params = $this->getSetting('ym_param');

        if ($params) {
            foreach ($params as $param) {
                if (empty($param['field'])) continue;
                $this->addField($param['field']);
            }
        }

        $this->insertField($desc);
        $this->insertField(array(
            'id',
            'parent',
            'href',
            'url_image',
            'price',
            'old_price',
            'images',
            'weight',
            'article',
            'vendor',
            'vendor_name',
            'pagetitle',
        ));

        $settingFields = array(
            'ym_delivery_default',
            'ym_delivery_field_options',
            'ym_pickup_default',
            'ym_pickup_field_options',
            'ym_available_field',
            'ym_store_field',
            'ym_model_field',
            'ym_type_prefix_field',
            'ym_vendor_code_field',
            'ym_group_id_field',
            'ym_sales_notes_field',
            'ym_country_of_origin_field',
        );
        foreach ($settingFields as $key) {
            $field = $this->getSetting($key);
            if (empty($field)) continue;
            $this->addField($field);
        }
    }

    /**
     * @return bool
     */
    public function beforeStart()
    {
        if ($ok = parent::beforeStart()) {
            $this->writer->writeCategories($this->getCategories(), $this->getSettings());
        }
        return $ok;
    }

    /**
     * @param array $data
     */
    public function work(array $data)
    {
        if (!empty($this->multicategories)) {
            if(isset($this->multicategories[$data['id']])) {
                $data['offerMulticategories'] = $this->multicategories[$data['id']];
                unset($this->multicategories[$data['id']]);
            }
        }
        if ($this->includeOptionsPrice2) {
            $modifications = $this->modx->call('msopModification', 'getProductModification', array(&$this->modx, $data['id']));
            if ($modifications) {
                foreach ($modifications as $modification) {
                    $modification = $this->prepareModification($modification, $data);
                    parent::work($modification);
                }
            } else {
                parent::work($data);
            }
        } else {
            if ($this->iemssaleprice) {
                $data['prices'] = $this->prepareSalePrices($data['id']);
            }
            parent::work($data);
        }
    }

    /**
     * @param int $rid
     * @return array
     */
    public function prepareSalePrices($rid)
    {
        $result = array();
        $tools = $this->iemssaleprice->getTools();
        $counts = $tools->getCountSalePrice(array($rid));
        if ($counts) {
            $exclude = $this->getSetting('ym_exclude_saleprice_price');
            $idx = $this->onlySalePricePrice ? 1 : 2;
            foreach ($counts as $count) {
                $key = $idx == 1 ? 'price' : 'price' . $idx;
                if (empty($exclude) || !in_array($key, $exclude)) {
                    $output = $tools->getMsSalePriceInstance()->getPrice($rid, $count);
                    if ($output['success'] && $output['data']['price']) {
                        $result[$key] = $this->formatPrice($output['data']['price']);
                    }
                }
                $idx++;
            }
        }
        return $result;
    }

    /**
     * @param array $modification
     * @param array $data
     * @return array
     */
    public function prepareModification(array $modification, array $data)
    {
        $modification['ieym_modification'] = $modification['id'];
        $modification['id'] = $data['id'] . 'M' . $modification['id'];
        $modification['price'] = $this->formatPrice($modification['price']);

        if (!empty($modification['name'])) {
            $modification['pagetitle'] = $modification['name'];
            unset($modification['name']);
        }
        if (!empty($modification['options'])) {
            $modification = $modification['options'] + $modification;
            unset($modification['options']);
        }
        if (!empty($modification['image'])) {
            if (!empty($data['images'][$modification['image']])) {
                $modification['images'] = array(
                    $data['images'][$modification['image']]
                );
            }
        }
        foreach ($data as $key => $val) {
            if (!isset($modification[$key]) || empty($modification[$key])) {
                $modification[$key] = $val;
            }
        }

        $modification = $this->prepareFieldHref('href', $modification, $modification, $this);

        return $modification;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldImages($field, array $data = array(), array $result, MsIeWorker &$worker)
    {
        $result[$field] = array();
        if (!empty($data['id'])) {
            $images = $this->iegallery->getTools()->getResourceGalleryImages($data['id'], $this->getSettings());
            if ($images) {
                foreach ($images as $image) {
                    $result[$field][$image['id']] = $this->tools->getSiteUrl() . $image['url'];
                }
            }
        }
        return $result;
    }


}