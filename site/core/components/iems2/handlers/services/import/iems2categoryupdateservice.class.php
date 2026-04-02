<?php

class IeMs2CategoryUpdateService extends MsIeResourceUpdateService
{

    /** @var int $rank */
    protected $rank = 3;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2categoryupdateservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2categoryupdateservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2categoryupdateservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2categoryupdateservice');
    }

    /**
     * @return string
     */
    public function getParentClassKey()
    {
        return 'msCategory';
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Category', $exclude, true),
            $this->tools->getTvFields('tv', 'TV')
        );
    }

    /**
     * @param array $properties
     * @return IeMs2CategoryUpdateWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2categoryupdateworker_worker_class', null, 'IeMs2CategoryUpdateWorker', true);
        return $this->loadWorker($className, $properties);
    }


}