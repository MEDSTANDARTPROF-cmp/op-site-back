<?php

class ieMsOptionsColorImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 12;

    public function initialize()
    {
        $this->modx->lexicon->load('iemsoptionscolor:iemsoptionscolorimportservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemsoptionscolor_iemsoptionscolorimportservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemsoptionscolor_iemsoptionscolorimportservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemsoptionscolor:iemsoptionscolorimportservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/iemsoptionscolorimportservice.js',
        );
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
        return true;
        $this->tools->hasAddition('msoptionscolor');
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
     * @return array
     */
    public function getCheckingFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', '', $exclude, true),
            $this->tools->getProductFields()
        );

    }

    /**
     * @param array $properties
     * @return ieMsOptionsColorImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemsoptionscolor_importservice_worker', null, 'ieMsOptionsColorImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}