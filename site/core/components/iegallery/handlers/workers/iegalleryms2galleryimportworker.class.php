<?php

class ieGalleryMs2GalleryImportWorker extends ieGalleryMs2ImagesImportWorker
{
    /** @var string $classKey */
    protected $classKey = 'modResource';
    /** @var string $resourceKey */
    protected $resourceKey = 'resource_id';
    /** @var string $galleryType */
    protected $galleryType = 'ms2gallery';
}