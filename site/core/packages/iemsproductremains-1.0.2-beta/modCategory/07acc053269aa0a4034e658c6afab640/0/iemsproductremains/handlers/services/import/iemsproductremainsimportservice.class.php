<?php

class ieMsProductRemainsImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 10;

    public function initialize()
    {
        $this->modx->lexicon->load('iemsproductremains:iemsproductremainsimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemsproductremains_iemsproductremainsimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemsproductremains_iemsproductremainsimportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemsproductremains:iemsproductremainsimportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/iemsproductremainsimportservice.js',
        );
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return array();
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
        return $this->tools->hasAddition('msproductremains');
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
     * @return array
     */
    public function getCheckingFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', '', $exclude, true),
            $this->tools->getProductFields()
        );

    }

    /**
     * @param array $properties
     * @return ieMsProductRemainsImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemsproductremains_importservice_worker', null, 'ieMsProductRemainsImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}