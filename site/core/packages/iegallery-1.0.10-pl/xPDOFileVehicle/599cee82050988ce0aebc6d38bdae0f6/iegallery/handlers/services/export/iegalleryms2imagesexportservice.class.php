<?php

class ieGalleryMs2ImagesExportService extends MsIeExportService
{

    /** @var int $rank */
    protected $rank = 5;

    public function initialize()
    {
        $this->modx->lexicon->load('iegallery:iegalleryms2imagesexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2imagesexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2imagesexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array(
            'iegallery:setting',
            'iegallery:iegalleryms2imagesexportservice'
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
            $this->config['jsUrl'] . 'mgr/setting/service/iegalleryexportservice.js',
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
        return $this->tools->hasAddition('minishop2')  && $this->tools->hasAddition('iems2');;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        $exclude[] = 'id';
        return array_merge(
            $this->tools->getResourceFields('', 'Product', $exclude, true),
            $this->tools->getProductFields('', 'Product',array('id'),true),
            $this->tools->getGalleryCustomFields('', 'Photo gallery'),
            $this->tools->getMs2ImagesFields('', 'Photo gallery', array('type'), true)
        );
    }

    /**
     * @param array $properties
     * @return ieGalleryMs2ImagesExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iegallery_iegalleryms2imagesexportservice_worker', null, 'ieGalleryMs2ImagesExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}