<?php

class IeMs2CategoryExportService extends MsIeResourceExportService
{

    /** @var int $rank */
    protected $rank = 2;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2categoryexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2categoryexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2categoryexportservice_description');
    }


    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2categoryexportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
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
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Category', $exclude, true),
            $this->tools->getResourceCustomFields('', 'Category', $exclude, true),
            $this->tools->getSeoProFields('', 'SeoPro'),
            $this->tools->getTvFields('tv.', 'TV', 'name')
        );
    }

    /**
     * @param array $properties
     * @return IeMs2CategoryExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2categoryexportservice_worker_class', null, 'IeMs2CategoryExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}