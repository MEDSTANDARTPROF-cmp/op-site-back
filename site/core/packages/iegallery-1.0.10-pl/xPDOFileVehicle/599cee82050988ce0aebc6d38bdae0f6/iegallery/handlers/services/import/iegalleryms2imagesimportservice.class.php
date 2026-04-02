<?php

class ieGalleryMs2ImagesImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 7;

    public function initialize()
    {
        $this->modx->lexicon->load('iegallery:iegalleryms2imagesimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2imagesimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2imagesimportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array(
            'iegallery:setting',
            'iegallery:iegalleryms2imagesimportservice'
        );
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/iegallery.js',
            $this->config['jsUrl'] . 'mgr/misc/combo.js',
            $this->config['jsUrl'] . 'mgr/setting/service/iegalleryimportservice.js',
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
            IeGallery.config = ' . $this->modx->toJSON($config) . ';
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
        return $this->tools->hasAddition('minishop2');
    }


    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Product', $exclude, true),
            $this->tools->getProductFields('', 'Product'),
            $this->tools->getGalleryCustomFields('', 'Photo gallery'),
            $this->tools->getMs2ImagesFields('', 'Photo gallery', array('id', 'type'), true)
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
     * @return ieGalleryMs2ImagesImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iegallery_iegalleryms2imagesimportservice_worker', null, 'ieGalleryMs2ImagesImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}