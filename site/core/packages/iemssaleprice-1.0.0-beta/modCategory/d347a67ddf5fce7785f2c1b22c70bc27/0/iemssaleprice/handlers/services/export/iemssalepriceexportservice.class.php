<?php

class ieMsSalePriceExportService extends MsIeExportService
{

    /** @var int $rank */
    protected $rank = 9;

    public function initialize()
    {
        $this->modx->lexicon->load('iemssaleprice:iemssalepriceexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemssaleprice_iemssalepriceexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemssaleprice_iemssalepriceexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemssaleprice:iemssalepriceexportservice');
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
        return $this->tools->hasAddition('mssaleprice')  && $this->tools->hasAddition('iems2');
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
     * @param array $properties
     * @return ieMsSalePriceExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemssaleprice_iemssalepriceexportservice_worker', null, 'ieMsSalePriceExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}