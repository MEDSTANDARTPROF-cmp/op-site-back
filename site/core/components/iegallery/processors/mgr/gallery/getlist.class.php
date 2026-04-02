<?php

class IeGalleryGetListProcessor extends modProcessor
{
    /** @var IeGallery $iegallery */
    public $iegallery;

    public function initialize()
    {
        $this->iegallery = $this->modx->getService('iegallery', 'IeGallery');
        return parent::initialize();
    }

    public function process()
    {
        $result = array();
        if ($this->iegallery->getTools()->hasAddition('minishop2')) {
            $result[] = array(
                'key' => 'minishop2',
                'name' => $this->modx->lexicon('iegallery_combo_gallery_ms2images')
            );
        }
        if ($this->iegallery->getTools()->hasAddition('ms2gallery')) {
            $result[] = array(
                'key' => 'ms2gallery',
                'name' => $this->modx->lexicon('iegallery_combo_gallery_ms2gallery')
            );
        }
        return $this->outputArray($result, count($result));
    }
}

return 'IeGalleryGetListProcessor';
