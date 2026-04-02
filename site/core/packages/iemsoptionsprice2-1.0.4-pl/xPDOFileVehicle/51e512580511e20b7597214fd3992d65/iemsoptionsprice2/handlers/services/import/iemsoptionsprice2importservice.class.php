<?php

class ieMsOptionsPrice2ImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 9;

    public function initialize()
    {
        $this->modx->lexicon->load('iemsoptionsprice2:iemsoptionsprice2importservice');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('iemsoptionsprice2_iemsoptionsprice2importservice_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('iemsoptionsprice2_iemsoptionsprice2importservice_description');
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('iemsoptionsprice2:iemsoptionsprice2importservice');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/iemsoptionsprice2importservice.js',
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
        return $this->tools->hasAddition('msoptionsprice');
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
            $this->tools->getProductFields('msopm_', 'MsOptionsPrice2', array('id'), true),
            $this->tools->getOptionsPrice2Fields('msopm_', 'MsOptionsPrice2'),
            $this->tools->getProductOptionsFields('msopm_', 'MsOptionsPrice2'),
            $this->tools->getOptionsPrice2CustomFields('msopm_', 'MsOptionsPrice2')
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
     * @return ieMsOptionsPrice2ImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        $className = $this->modx->getOption('iemsoptionsprice2_importservice_worker', null, 'ieMsOptionsPrice2ImportWorker', true);
        return $this->loadWorker($className, $properties);
    }


}