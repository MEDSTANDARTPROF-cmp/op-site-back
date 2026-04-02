<?php

class IeYandexMarketExportService extends MsIeExportService
{

    /** @var int $rank */
    protected $rank = 11;

    public function initialize()
    {
        $this->modx->lexicon->load('ieyandexmarket:ieyandexmarketexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('ieyandexmarket_ieyandexmarketexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('ieyandexmarket_ieyandexmarketexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('ieyandexmarket:ieyandexmarketexportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/ieyandexmarket.js',
            $this->config['jsUrl'] . 'mgr/misc/combo.js',
            $this->config['jsUrl'] . 'mgr/misc/options.grid.js',
            $this->config['jsUrl'] . 'mgr/param/param.grid.js',
            $this->config['jsUrl'] . 'mgr/options/pickup.options.grid.js',
            $this->config['jsUrl'] . 'mgr/options/delivery.options.grid.js',
            $this->config['jsUrl'] . 'mgr/setting/service/ieyandexmarketexportservice.js',
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
            IeYandexMarket.config = ' . $this->modx->toJSON($config) . ';
        });
        </script>';
    }

    /**
     * @return array
     */
    public function getCss()
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
     * @return bool
     */
    public function isEnabled()
    {
        return $this->tools->hasAddition('minishop2') && $this->tools->hasAddition('iems2');
    }

    /**
     * @return array
     */
    public static function getAllowedFileExtensions()
    {
        return array('xml');
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        //$exclude = $this->getExcludeFields();
        return parent::getCustomFields();
    }

    /**
     * @param array $properties
     * @return IeYandexMarketExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('ieyandexmarket_ieyandexmarketexportservice_worker', null, 'IeYandexMarketExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}