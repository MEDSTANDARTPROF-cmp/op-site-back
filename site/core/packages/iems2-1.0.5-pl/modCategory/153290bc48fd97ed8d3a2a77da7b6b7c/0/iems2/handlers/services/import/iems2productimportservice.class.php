<?php

class IeMs2ProductImportService extends IeMs2CategoryImportService
{

    /** @var int $rank */
    protected $rank = 4;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2productimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2productimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2productimportservice_description');
    }


    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2productimportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/iems2productimportservice.js',
        );
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getProductCustomFields('', ' Product', array('categories')),
            $this->tools->getSeoProFields('', 'SeoPro'),
            $this->tools->getResourceFields('', ' Product', $exclude, true),
            $this->tools->getProductFields('', ' Product'),
            $this->tools->getProductOptionsFields('options-', ' Option'),
            $this->tools->getTvFields('tv', 'TV')
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
            $this->tools->getProductFields('', '')
        );

    }

    /**
     * @param array $properties
     * @return IeMs2ProductImportWorker|null
     */
    public function getWorker(array $properties = array())
    {

        $className = $this->modx->getOption('iems2_iems2productimportworker_worker_class', null, 'IeMs2ProductImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}