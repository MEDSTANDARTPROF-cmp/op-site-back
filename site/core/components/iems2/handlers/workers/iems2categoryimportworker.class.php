<?php

class IeMs2CategoryImportWorker extends MsIeResourceImportWorker
{
    /** @var string $classKey */
    protected $classKey = 'msCategory';

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->templateDefault = $this->modx->getOption('ms2_template_category_default', null, 0, true);
        }
        return $initialized;
    }



}