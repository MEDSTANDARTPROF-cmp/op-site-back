<?php

class ieMsSalePriceImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 11;

    public function initialize()
    {
        $this->modx->lexicon->load('iemssaleprice:iemssalepriceimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemssaleprice_iemssalepriceimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemssaleprice_iemssalepriceimportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemssaleprice:iemssalepriceimportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/iemssalepriceimportservice.js',
        );
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
        return $this->tools->hasAddition('mssaleprice');
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
            $this->tools->getSalePriceFields('mssp_', 'msSalePrice')
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
     * @return ieMsSalePriceImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemssaleprice_iemssalepriceimportservice_worker', null, 'ieMsSalePriceImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}