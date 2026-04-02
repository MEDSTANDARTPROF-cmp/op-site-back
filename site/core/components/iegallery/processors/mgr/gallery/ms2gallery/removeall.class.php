<?php


class IeGalleryMs2GalleryRemoveProcessor extends modObjectProcessor
{
    public $classKey = 'msResourceFile';
    public $languageTopics = array('ms2gallery:default');
    /** @var ms2Gallery $ms2Gallery */
    public $ms2Gallery;

    /**
     * @param modX $modx
     * @param array $properties
     */
    function __construct(modX &$modx, array $properties = array())
    {
        $this->ms2Gallery = $modx->getService('ms2gallery', 'ms2Gallery', MODX_CORE_PATH . 'components/ms2gallery/model/ms2gallery/');
        parent::__construct($modx, $properties);
    }

    /**
     * @return array|string
     */
    public function process()
    {
        $resource_id = (int)$this->getProperty('resource_id');
        if (empty($resource_id)) {
            return $this->failure($this->modx->lexicon('ms2gallery_err_ns'));
        }

        /** @var msResourceFile $file */
        foreach ($this->modx->getCollection('msResourceFile', ['resource_id' => $resource_id, 'parent' => 0]) as $file) {
            $file->remove();
        }

        if ($this->modx->getOption('ms2gallery_sync_ms2')) {

            $this->ms2Gallery->rankResourceImages($resource_id, true);
            /** @var msProductData $product */
            if ($product = $this->modx->getObject('msProductData', (int)$resource_id)) {
                $this->ms2Gallery->syncFiles('ms2', $resource_id);
                if ($thumb = $product->updateProductImage()) {
                    return $this->modx->error->success('', array('thumb' => $thumb));
                }
            }
        }
        return $this->success();
    }
}

return 'IeGalleryMs2GalleryRemoveProcessor';