<?php

class IeMs2CategoryImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 2;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2categoryimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2categoryimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2categoryimportservice_description');
    }


    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2categoryimportservice');
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
            $this->tools->getSeoProFields('', 'SeoPro'),
            $this->tools->getTvFields('tv', 'TV')
        );
    }

    /**
     * @return array
     */
    public function getCheckingFields()
    {
        $exclude = $this->getExcludeFields();
        return $this->tools->getResourceFields('', '', $exclude, true);

    }

    /**
     * @param array $properties
     * @return IeMs2CategoryImportWorker
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2categoryimportworker_worker_class', null, 'IeMs2CategoryImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}