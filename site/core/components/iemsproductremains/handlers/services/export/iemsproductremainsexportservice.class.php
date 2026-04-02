<?php

class ieMsProductRemainsExportService extends MsIeExportService
{

    /** @var int $rank */
    protected $rank = 8;

    public function initialize()
    {
        $this->modx->lexicon->load('iemsproductremains:iemsproductremainsexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemsproductremains_iemsproductremainsexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemsproductremains_iemsproductremainsexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemsproductremains:iemsproductremainsexportservice');
    }


    /**
     * @return string
     */
    public function getParentClassKey()
    {
        return 'msCategory';
    }


    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->tools->hasAddition('msproductremains')  && $this->tools->hasAddition('iems2');;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Product', $exclude, true),
            $this->tools->getProductFields('', 'Product', array('id'), true),
            $this->tools->getProductRemainsFields('mspr_', 'msProductRemains', array('options'), true)
        );
    }

    /**
     * @param array $properties
     * @return ieMsProductRemainsExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemsproductremains_exportservice_worker', null, 'ieMsProductRemainsExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}