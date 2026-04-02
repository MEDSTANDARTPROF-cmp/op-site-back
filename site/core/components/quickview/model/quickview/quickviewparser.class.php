<?php

if (!class_exists('modParser')) {
    /** @noinspection PhpIncludeInspection */
    require_once MODX_CORE_PATH . 'model/modx/modparser.class.php';
}

class QuickViewParser extends modParser
{

    function __construct(xPDO &$modx)
    {
        parent::__construct($modx);
    }

    public function setProcessingUncacheable($uncacheable = true)
    {
        $this->_processingUncacheable = $uncacheable;

        return $this->_processingUncacheable;
    }

}