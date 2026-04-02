<?php

class IeMs2ProductExportService extends IeMs2CategoryExportService
{

    /** @var int $rank */
    protected $rank = 3;

    public function initialize()
    {
        $this->modx->lexicon->load('iems2:iems2productexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iems2_iems2productexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iems2_iems2productexportservice_description');
    }


    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iems2:iems2productexportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/iems2.js',
            $this->config['jsUrl'] . 'mgr/vendor/tree.vendor.js',
            $this->config['jsUrl'] . 'mgr/setting/service/iems2productexportservice.js',
        );
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $config = array(
            'connector_url' => $this->tools->config['connector_url']
        );
        return '<script type="text/javascript">
           Ext.onReady(function() {
            IeMs2.config = ' . $this->modx->toJSON($config) . ';
        });
        </script>';
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getProductCustomFields('', 'Product'),
            $this->tools->getSeoProFields('', 'SeoPro'),
            $this->tools->getResourceCustomFields('', 'Product', $exclude, true),
            $this->tools->getResourceFields('', ' Product', $exclude, true),
            $this->tools->getProductFields('', ' Product'),
            $this->tools->getProductOptionsFields('', ' Option'),
            $this->tools->getTvFields('tv.', 'TV', 'name')
        );
    }

    /**
     * @param array $properties
     * @return IeMs2ProductExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iems2_iems2productexportservice_worker_class', null, 'IeMs2ProductExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}