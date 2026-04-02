<?php

use Bukashk0zzz\YmlGenerator\Model\Offer\OfferInterface;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferArtistTitle;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferAudiobook;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferBook;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferCustom;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferEventTicket;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferSimple;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferTour;
use Bukashk0zzz\YmlGenerator\Model\Offer\OfferParam;
use Bukashk0zzz\YmlGenerator\Model\Category;
use Bukashk0zzz\YmlGenerator\Model\Currency;
use Bukashk0zzz\YmlGenerator\Model\Delivery;
use Bukashk0zzz\YmlGenerator\Model\Pickup;
use Bukashk0zzz\YmlGenerator\Model\ShopInfo;
use Bukashk0zzz\YmlGenerator\Cdata;
use Bukashk0zzz\YmlGenerator\Settings;
use Bukashk0zzz\YmlGenerator\Generator;

// https://github.com/Bukashk0zzz/YmlGenerator

class IeYandexMarketXMLWriter extends MsIeWriter
{
    /** @var array $offers */
    public $offers = array();
    /** @var array $categories */
    public $categories = array();
    /** @var array $params */
    protected $params = array();
    /** @var array $descriptionFields */
    protected $descriptionFields = array();
    /** @var string $descAllowedTags */
    protected $descAllowedTags;
    /** @var string $offerType */
    protected $offerType;
    /** @var string $defaultCurrency */
    protected $defaultCurrency;
    /** @var string $defaultPickup */
    protected $defaultPickup;
    /** @var string $pickupField */
    protected $pickupField;
    /** @var string $pickupFieldOptions */
    protected $pickupFieldOptions;
    /** @var string $defaultDelivery */
    protected $defaultDelivery;
    /** @var string $deliveryField */
    protected $deliveryField;
    /** @var string $deliveryFieldOptions */
    protected $deliveryFieldOptions;
    /** @var string $defaultSalesNotes */
    protected $defaultSalesNotes;
    /** @var string $salesNotesField */
    protected $salesNotesField;
    /** @var string $availableField */
    protected $availableField;
    /** @var string $modelField */
    protected $modelField;
    /** @var string $typePrefixField */
    protected $typePrefixField;
    /** @var string $vendorСodeField */
    protected $vendorСodeField;
    /** @var string $expiryField */
    protected $expiryField;
    /** @var string $groupIdField */
    protected $groupIdField;
    /** @var string $countryOfOriginField */
    protected $countryOfOriginField;
    /** @var string $storeField */
    protected $storeField;

    /**
     * @param array $config
     * @return bool
     */
    public function initialize(array $config = array())
    {
        if ($ok = parent::initialize($config)) {
            $this->offerType = $this->modx->getOption('ym_offer_type', $this->config, '', true);
            $this->defaultCurrency = $this->modx->getOption('ym_default_currency', $this->config, 'RUB', true);
            $this->defaultStore = $this->modx->getOption('ym_store_default', $this->config, true, true);
            $this->defaultStore = $this->prepareBool($this->defaultStore);
            $this->storeField = $this->modx->getOption('ym_store_field', $this->config, '', true);
            $this->defaultPickup = $this->modx->getOption('ym_pickup_default', $this->config, true, true);
            $this->defaultPickup = $this->prepareBool($this->defaultPickup);
            $this->pickupField = $this->modx->getOption('ym_pickup_field', $this->config, '', true);
            $this->pickupFieldOptions = $this->modx->getOption('ym_pickup_field_options', $this->config, '', true);
            $this->defaultDelivery = $this->modx->getOption('ym_delivery_default', $this->config, true, true);
            $this->defaultDelivery = $this->prepareBool($this->defaultDelivery);
            $this->deliveryField = $this->modx->getOption('ym_delivery_field', $this->config, '', true);
            $this->deliveryFieldOptions = $this->modx->getOption('ym_delivery_field_options', $this->config, '', true);
            $this->availableField = $this->modx->getOption('ym_available_field', $this->config, '', true);
            $this->salesNotesField = $this->modx->getOption('ym_sales_notes_field', $this->config, '', true);
            $this->defaultSalesNotes = $this->modx->getOption('ym_sales_notes_default', $this->config, '', true);
            $this->vendorСodeField = $this->modx->getOption('ym_vendor_code_field', $this->config, '', true);
            $this->modelField = $this->modx->getOption('ym_model_field', $this->config, '', true);
            $this->typePrefixField = $this->modx->getOption('ym_type_prefix_field', $this->config, '', true);
            $this->expiryField = $this->modx->getOption('ym_expiry_field', $this->config, '', true);
            $this->groupIdField = $this->modx->getOption('ym_group_id_field', $this->config, '', true);
            $this->countryOfOriginField = $this->modx->getOption('ym_country_of_origin_field', $this->config, '', true);
            $this->descriptionFields = $this->modx->getOption('ym_description_fields', $this->config, array(), true);
            $this->descAllowedTags = $this->modx->getOption('ym_desc_allowed_tags', $this->config, '', true);
            $this->params = $this->modx->getOption('ym_param', $this->config, array(), true);
        }
        return $ok;
    }

    /**
     * @param array $data
     * @param array $options
     * @return OfferInterface
     */
    public function write(array $data, array $options = array())
    {
        $offer = $this->createOffer();
        $available = 'true';
        $salesNotes = $this->defaultSalesNotes;
        $store = $this->defaultStore;
        $pickup = $this->defaultPickup;
        $delivery = $this->defaultDelivery;

        if ($this->availableField) {
            $available = $this->getDataFieldValue($this->availableField, $data, 'true');
        }
        if ($this->storeField) {
            $store = $this->getDataFieldValue($this->storeField, $data, $this->defaultStore);
        }
        if ($this->pickupField) {
            $pickup = $this->getDataFieldValue($this->pickupField, $data, $this->defaultPickup);
        }
        if ($this->deliveryField) {
            $delivery = $this->getDataFieldValue($this->deliveryField, $data, $this->defaultDelivery);
        }
        if ($this->salesNotesField) {
            $salesNotes = $this->getDataFieldValue($this->salesNotesField, $data, $this->defaultSalesNotes);
        }

        $salesNotes = strip_tags($salesNotes);

        if ($this->vendorСodeField) {
            $vendorСode = $this->modx->getOption($this->vendorСodeField, $data, '', true);
            if ($vendorСode) {
                $offer->setVendorCode($vendorСode);
            }
        }
        if ($this->modelField && $this->offerType == 'vendor.model') {
            $model = $this->modx->getOption($this->modelField, $data, '', true);
            if ($model) {
                $offer->setModel($model);
            }
        }
        if ($this->typePrefixField) {
            $typePrefix = $this->modx->getOption($this->typePrefixField, $data, '', true);
            if ($typePrefix) {
                $offer->setTypePrefix($typePrefix);
            }
        }
        if ($this->groupIdField) {
            $groupId = $this->modx->getOption($this->groupIdField, $data, '', true);
            if ($groupId) {
                $offer->setGroupId($groupId);
            }
        }
        if ($this->countryOfOriginField) {
            $countryOfOrigin = $this->modx->getOption($this->countryOfOriginField, $data, '', true);
            if ($countryOfOrigin) {
                $offer->setCountryOfOrigin($countryOfOrigin);
            }
        }

        /*if ($this->expiryField) {
            $expiry = $this->modx->getOption($this->expiryField, $data, '', true);
            if ($expiry) {
            }
        }*/


        $offer->setId($data['id']);
        $offer->setUrl($data['href']);
        $offer->setName($this->fixUtf8($data['pagetitle']));

        if (!empty($data['offerMulticategories'])) {
            $offer->setCategoriesId($data['offerMulticategories']);
        } else {
            $offer->setCategoryId($data['parent']);
        }
        $offer->setCurrencyId($this->defaultCurrency);
        $offer->setAvailable($available);
        $offer->setStore($store);
        $offer->setPickup($pickup);
        $offer->setDelivery($delivery);
        $offer->setSalesNotes($salesNotes);
        $offer->setPrice($data['price']);

        if (!empty($data['prices'])) {
            foreach ($data['prices'] as $tag => $price) {
                if ($tag == 'price') {
                    $offer->setPrice($price);
                } else {
                    $offer->addCustomElement($tag, $price);
                }
            }
        }

        if (!empty($data['old_price']) && $data['old_price'] > 0) {
            $offer->setOldPrice($data['old_price']);
        }

        if (!empty($data['images'])) {
            $offer->setPictures($data['images']);
        }

        if (!empty($data['vendor_name'])) {
            $offer->setVendor($this->fixUtf8($data['vendor_name']));
        }

        if (!empty($data['weight']) && $data['weight'] > 0) {
            $offer->setWeight($data['weight']);
        }

        $description = $this->prepareDescription($data, $options);
        if ($description) {
            $offer->setDescription(new Cdata($description));
        }

        $this->setOfferParams($offer, $data, $options);
        $this->setOfferPickupOptions($offer, $data, $options);
        $this->setOfferDeliveryOptions($offer, $data, $options);

        $this->offers[] = $offer;
        $this->offset++;
        return true;
    }

    /**
     * @param array $categories
     * @param array $options
     * @return array
     */
    public function writeCategories(array $categories, array $options = array())
    {
        if ($categories) {
            foreach ($categories as $item) {
                $category = new Category();
                $category
                    ->setId($item['id'])
                    ->setName($item['pagetitle']);
                if (!empty($item['parent'])) {
                    $category->setParentId($item['parent']);
                }
                $this->categories[] = $category;
            }
        }
        return $this->categories;
    }

    /**
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function save($path, array $options = array())
    {
        $version = $this->modx->getVersionData();
        $settings = (new Settings())
            ->setOutputFile($path)
            ->setEncoding('UTF-8');

        $siteName = $this->modx->getOption('site_name', $options, '', true);
        $company = $this->modx->getOption('ym_store_company', $options, '', true);
        $email = $this->modx->getOption('ym_store_email', $options, '', true);

        $shopInfo = (new ShopInfo())
            ->setName($this->modx->getOption('ym_store_name', $options, $siteName, true))
            ->setPlatform('MODX ' . $version['code_name'] . ' ' . $version['full_version'])
            ->setUrl($this->modx->getOption('ym_site_url', $options, '', true));

        if ($company) {
            $shopInfo->setCompany($company);
        }

        if ($email) {
            $shopInfo->setEmail($email);
        }

        $currencies = [];
        $rateCurrency = $this->modx->getOption('ym_rate_currency', $options, 'CBRF', true);
        $arrCurrencies = $this->modx->getOption('ym_currencies', $options, array('RUB'), true);

        if (!in_array($this->defaultCurrency, $arrCurrencies)) {
            $arrCurrencies[] = $this->defaultCurrency;
        }

        foreach ($arrCurrencies as $currency) {
            $rate = $currency == $this->defaultCurrency ? 1 : $rateCurrency;
            $currencies[] = (new Currency())
                ->setId($currency)
                ->setRate($rate);
        }

        $deliveries = [];
        $arrDeliveries = $this->modx->getOption('ym_delivery_options', $options, array(), true);
        if ($arrDeliveries) {
            foreach ($arrDeliveries as $delivery) {
                if (empty($delivery['cost']) || empty($delivery['days'])) continue;
                if (!isset($delivery['order_before'])) $delivery['order_before'] = '';
                $deliveries[] = (new Delivery())
                    ->setCost($delivery['cost'])
                    ->setDays($delivery['days'])
                    ->setOrderBefore($delivery['order_before']);
            }
        }

        $pickups = [];
        $arrPickups = $this->modx->getOption('ym_pickup_options', $options, array(), true);
        if ($arrPickups) {
            foreach ($arrPickups as $pickup) {
                if (empty($pickup['cost']) || empty($pickup['days'])) continue;
                if (!isset($pickup['order_before'])) $pickup['order_before'] = '';
                $pickups[] = (new Pickup())
                    ->setCost($pickup['cost'])
                    ->setDays($pickup['days'])
                    ->setOrderBefore($pickup['order_before']);
            }
        }

        (new Generator($settings))->generate(
            $shopInfo,
            $currencies,
            $this->categories,
            $this->offers,
            $deliveries,
            $pickups
        );
        unset($pickups, $shopInfo, $currencies, $deliveries);
        return true;
    }

    public function close()
    {
        unset($this->offers);
        unset($this->categories);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_XML;
    }

    /**
     * @return OfferArtistTitle|OfferAudiobook|OfferBook|OfferCustom|OfferEventTicket|OfferSimple|OfferTour
     */
    public function createOffer()
    {
        switch ($this->offerType) {
            case 'tour':
                $offer = new OfferTour();
                break;
            case 'book':
                $offer = new OfferBook();
                break;
            case 'audiobook':
                $offer = new OfferAudiobook();
                break;
            case 'vendor.model':
                $offer = new OfferCustom();
                break;
            case 'artist.title':
                $offer = new OfferArtistTitle();
                break;
            case 'event-ticket':
                $offer = new OfferEventTicket();
                break;
            default:
                $offer = new OfferSimple();
        }
        return $offer;
    }


    /**
     * @param array $data
     * @param array $options
     * @return string
     */
    public function prepareDescription(array $data, array $options = array())
    {
        $desc = '';
        if ($this->descriptionFields) {
            foreach ($this->descriptionFields as $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $desc = $data[$field];
                    if ($this->descAllowedTags) {
                        $desc = strip_tags($desc, $this->descAllowedTags);
                    }
                    return $this->fixUtf8($desc);
                }
            }
        }

        return $this->fixUtf8($desc);
    }

    /**
     * @param OfferArtistTitle|OfferAudiobook|OfferBook|OfferCustom|OfferEventTicket|OfferSimple|OfferTour $offer
     * @param array $data
     * @param array $options
     */
    public function setOfferParams(&$offer, array $data, array $options = array())
    {
        if ($this->params) {
            foreach ($this->params as $param) {
                if (empty($param['field']) || empty($data[$param['field']])) continue;
                $values = $data[$param['field']];
                if (!is_array($values)) {
                    $values = array($values);
                }
                foreach ($values as $val) {
                    if (empty($val)) continue;
                    $offerParam = new OfferParam();
                    $offerParam->setValue($val);
                    if (!empty($param['unit'])) {
                        $offerParam->setUnit($param['unit']);
                    }
                    if (!empty($param['name'])) {
                        $name = $param['name'];
                    } else {
                        $name = $this->modx->lexicon('ms2_product_' . $param['field']);
                    }
                    $offerParam->setName($name);
                    $offer->addParam($offerParam);
                }
            }
        }
    }

    /**
     * @param OfferArtistTitle|OfferAudiobook|OfferBook|OfferCustom|OfferEventTicket|OfferSimple|OfferTour $offer
     * @param array $data
     * @param array $options
     */
    public function setOfferPickupOptions(&$offer, array $data, array $options = array())
    {
        if ($this->pickupFieldOptions) {
            $pickupOptions = $this->modx->getOption($this->pickupFieldOptions, $data, '', true);
            if ($pickupOptions) {
                $pickupOptions = $this->modx->fromJSON($pickupOptions);
                if (is_array($pickupOptions)) {
                    foreach ($pickupOptions as $item) {
                        if (empty($item['cost']) || !isset($item['days']) || $item['days'] == '') continue;
                        $pickup = new Pickup();
                        $pickup->setCost($item['cost']);
                        $pickup->setDays($item['days']);
                        if (!empty($item['order_before'])) {
                            $pickup->setOrderBefore($item['order_before']);
                        }
                        $offer->addPickupOption($pickup);
                    }
                }
            }
        }
    }

    /**
     * @param OfferArtistTitle|OfferAudiobook|OfferBook|OfferCustom|OfferEventTicket|OfferSimple|OfferTour $offer
     * @param array $data
     * @param array $options
     */
    public function setOfferDeliveryOptions(&$offer, array $data, array $options = array())
    {
        if ($this->deliveryFieldOptions) {
            $deliveryOptions = $this->modx->getOption($this->deliveryFieldOptions, $data, '', true);
            if ($deliveryOptions) {
                $deliveryOptions = $this->modx->fromJSON($deliveryOptions);
                if (is_array($deliveryOptions)) {
                    foreach ($deliveryOptions as $item) {
                        if (empty($item['cost']) || !isset($item['days']) || $item['days'] == '') continue;
                        $delivery = new Delivery();
                        $delivery->setCost($item['cost']);
                        $delivery->setDays($item['days']);
                        if (!empty($item['order_before'])) {
                            $delivery->setOrderBefore($item['order_before']);
                        }
                        $offer->addDeliveryOption($delivery);
                    }
                }
            }
        }
    }

    /**
     * @param mixed $val
     * @return string
     */
    public function prepareBool($val)
    {
        $val = filter_var($val, FILTER_VALIDATE_BOOLEAN);
        return $val ? 'true' : 'false';
    }


    /**
     * @param string $field
     * @param array $data
     * @param mixed $default
     *
     * @return mixed|null
     */
    protected function getDataFieldValue(string $field, array $data, $default = null)
    {
        $value = $default;
        if (isset($data[$field])) {
            $value = $data[$field];
            if (is_array($value)) {
                if (isset($data[$field . '.value'])) {
                    $value = $data[$field . '.value'];
                } else if (isset($value[0])) {
                    $value = $value[0];
                }
            }
        }
        return $value;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function fixUtf8(string $content)
    {
        return preg_replace('/[\x00-\x1F\x7F]/', '', $content);
    }
}
