<?php

class IeMs2LinksImportService extends IeMs2ProductImportService
{

    /** @var int $rank */
    protected $rank = 6;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2linksimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2linksimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2linksimportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2linksimportservice');
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return array_merge(
            $this->tools->getResourceFields('master_', 'Master product', array('pagetitle')),
            $this->tools->getProductFields('master_', 'Master product'),
            $this->tools->getResourceFields('slave_', 'Slave product', array('pagetitle')),
            $this->tools->getProductFields('slave_', 'Slave product'),
            $this->tools->getProductLinksFields('', 'Link')
        );
    }

    /**
     * @param array $properties
     * @return IeMs2LinksImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2linksimportservice_worker_class', null, 'IeMs2LinksImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}