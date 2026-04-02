<?php

class IeMs2LinksExportService extends IeMs2ProductExportService
{

    /** @var int $rank */
    protected $rank = 4;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2linksexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2linksexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2linksexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2linksexportservice');
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
     * @return IeMs2LinksExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2linksexportservice_worker_class', null, 'IeMs2LinksExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}