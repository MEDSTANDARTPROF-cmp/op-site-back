<?php

class ieGalleryMs2GalleryExportService extends ieGalleryMs2ImagesExportService
{

    /** @var int $rank */
    protected $rank = 6;

    public function initialize()
    {
        $this->modx->lexicon->load('iegallery:iegalleryms2galleryexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2galleryexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2galleryexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array(
            'iegallery:setting',
            'iegallery:iegalleryms2galleryexportservice'
        );
    }


    /**
     * @return string
     */
    public function getParentClassKey()
    {
        return 'modResource';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->tools->hasAddition('ms2gallery')  && $this->tools->hasAddition('iems2');;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        $exclude[] = 'id';
        return array_merge(
            $this->tools->getResourceFields('', 'Resource', $exclude, true),
            $this->tools->getGalleryCustomFields('', 'Photo gallery'),
            $this->tools->getMs2GalleryFields('', 'Photo gallery', array( 'type'), true)
        );
    }

    /**
     * @param array $properties
     * @return ieGalleryMs2GalleryExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iegallery_iegalleryms2galleryexportservice_worker', null, 'ieGalleryMs2GalleryExportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}