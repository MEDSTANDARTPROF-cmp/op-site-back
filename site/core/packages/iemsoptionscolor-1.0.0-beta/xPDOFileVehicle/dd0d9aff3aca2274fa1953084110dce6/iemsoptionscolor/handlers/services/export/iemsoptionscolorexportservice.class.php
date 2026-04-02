<?php

class ieMsOptionsColorExportService extends MsIeExportService
{

    /** @var int $rank */
    protected $rank = 10;

    public function initialize()
    {
        $this->modx->lexicon->load('iemsoptionscolor:iemsoptionscolorexportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemsoptionscolor_iemsoptionscolorexportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemsoptionscolor_iemsoptionscolorexportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemsoptionscolor:iemsoptionscolorexportservice');
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
        return $this->tools->hasAddition('msoptionscolor')  && $this->tools->hasAddition('iems2');;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Product', $exclude, true),
            $this->tools->getProductFields('', 'Product', array('id'), true),
            $this->tools->getOptionsColorFields('msoc_', 'msOptionsColor')
        );
    }

    /**
     * @param array $properties
     * @return ieMsOptionsColorExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemsoptionscolor_exportservice_worker',null,'ieMsOptionsColorExportWorker',true);
        return $this->loadWorker($className, $properties);
    }


}