<?php

class ieGalleryMs2GalleryImportService extends ieGalleryMs2ImagesImportService
{

    /** @var int $rank */
    protected $rank = 8;

    public function initialize()
    {
        $this->modx->lexicon->load('iegallery:iegalleryms2galleryimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2galleryimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iegallery_iegalleryms2galleryimportservice_description');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->tools->hasAddition('ms2gallery');
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
            $this->tools->getMs2GalleryFields('', 'Photo gallery', array('id', 'type'), true)
        );
    }

    /**
     * @param array $properties
     * @return ieGalleryMs2GalleryImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iegallery_iegalleryms2galleryimportservice_worker', null, 'ieGalleryMs2GalleryImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}