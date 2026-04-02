<?php

class IeMs2VendorGetNodesProcessor extends modProcessor
{

    /** @var Msie $msie */
    protected $msie;
    /** @var array $vendors */
    protected $vendors = array();

    /** {@inheritDoc} */
    public function initialize()
    {
        $this->setDefaultProperties(array(
            'id' => 0,
            'sort' => 'name',
            'dir' => 'ASC',
        ));
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $presetId = $this->getProperty('preset', 0);
        if ($presetId) {
            /** @var MsiePreset $preset */
            if ($preset = $this->modx->getObject('MsiePreset', $presetId)) {
                $vendors = $preset->getSetting('vendors', '');
                $this->vendors = $this->msie->getTools()->explodeAndClean($vendors);
            }
        }
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process()
    {

        $list = array();
        $vendors = $this->getVendors();
        /** @var msVendor $vendor */
        foreach ($vendors['results'] as $vendor) {
            $vendorArray = $this->prepareVendor($vendor);
            if (!empty($vendorArray)) {
                $list[] = $vendorArray;
            }
        }
        return $this->toJSON($list);
    }

    /**
     * @return array
     */
    public function getVendors()
    {
        $data = array();
        $c = $this->modx->newQuery('msVendor');
        $data['total'] = $this->modx->getCount('msVendor', $c);
        $c->sortby($this->getProperty('sort'), $this->getProperty('dir'));
        $data['results'] = $this->modx->getCollection('msVendor', $c);
        return $data;
    }

    public function prepareVendor($vendor)
    {
        $cls = 'vendor';
        $count = 0;
        return array(
            'text' => htmlentities($vendor->get('name'), ENT_QUOTES, 'UTF-8') . ' (' . $vendor->get('id') . ')',
            'id' => 'n_ug_' . $vendor->get('id'),
            'pk' => $vendor->get('id'),
            'leaf' => ($count > 0 ? false : true),
            'type' => 'vendor',
            'qtip' => $vendor->get('description'),
            'cls' => $cls,
            'iconCls' => 'icon icon-address-card',
            'checked' => in_array($vendor->get('id'), $this->vendors),
        );
    }

}

return 'IeMs2VendorGetNodesProcessor';