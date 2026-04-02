<?php

class ieMsOptionsPrice2ExportService extends MsIeExportService
{

    /** @var int $rank */
    protected $rank = 7;

    public function initialize()
    {
        $this->modx->lexicon->load('iemsoptionsprice2:iemsoptionsprice2exportservice');

    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemsoptionsprice2_iemsoptionsprice2exportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemsoptionsprice2_iemsoptionsprice2exportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemsoptionsprice2:iemsoptionsprice2exportservice');
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
        return $this->tools->hasAddition('msoptionsprice') && $this->tools->hasAddition('iems2');
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
            $this->tools->getOptionsPrice2Fields('msopm_', 'MsOptionsPrice2'),
            $this->tools->getProductFields('msopm_', 'MsOptionsPrice2'),
            $this->tools->getProductOptionsFields('msopm_', 'MsOptionsPrice2'),
            $this->tools->getOptionsPrice2CustomFields('msopm_', 'MsOptionsPrice2')
        );
    }

    /**
     * @param array $properties
     * @return ieMsOptionsPrice2ExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemsoptionsprice2_exportservice_worker', null, 'ieMsOptionsPrice2ExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}