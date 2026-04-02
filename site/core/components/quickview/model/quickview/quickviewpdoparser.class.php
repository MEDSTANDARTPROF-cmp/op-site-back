<?php

if (!class_exists('pdoParser')) {
    /** @noinspection PhpIncludeInspection */
    require MODX_CORE_PATH . 'components/pdotools/model/pdotools/pdoparser.class.php';
}

class QuickViewPdoParser extends pdoParser
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