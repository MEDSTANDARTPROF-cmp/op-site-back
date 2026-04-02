<?php

class ieGalleryMs2GalleryExportWorker extends ieGalleryMs2ImagesExportWorker
{

    /** @var string $classKey */
    protected $classKey = 'msResourceFile';
    /** @var string $resourceKey */
    protected $resourceKey = 'resource_id';


    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $corePath = $this->modx->getOption('ms2gallery.core_path', null, $this->modx->getOption('core_path') . 'components/ms2gallery/');
            $this->modx->addPackage('ms2gallery', "{$corePath}model/");
        }
        return $initialized;
    }

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = parent::buildQueryConfig();
        unset($config['leftJoin']['Data']);
        unset($config['select']['Data']);
        return $config;
    }
}