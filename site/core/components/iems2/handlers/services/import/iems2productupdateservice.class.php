<?php

class IeMs2ProductUpdateService extends MsIeResourceUpdateService
{

    /** @var int $rank */
    protected $rank = 5;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2productupdateservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2productupdateservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2productupdateservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2productupdateservice');
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return array_merge(
            parent::getCustomFields(),
            $this->tools->getProductFields('', '')
        );
    }

    /**
     * @return array
     */
    public function getCheckingFields()
    {
        return array_merge(
            parent::getCheckingFields(),
            $this->tools->getProductFields('', '')
        );
    }

    /**
     * @param array $properties
     * @return IeMs2ProductUpdateWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2productupdateworker_worker_class', null, 'IeMs2ProductUpdateWorker', true);
        return $this->loadWorker($className, $properties);
    }
}