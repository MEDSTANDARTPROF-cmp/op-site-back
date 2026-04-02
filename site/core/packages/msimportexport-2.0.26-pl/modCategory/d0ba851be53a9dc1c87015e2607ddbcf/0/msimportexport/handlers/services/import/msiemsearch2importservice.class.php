<?php

class MsIeMSearch2ImportService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 2;

    public function initialize()
    {
        $this->modx->lexicon->load('msimportexport:service_msearch2');
    }


    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('msimportexport:service_msearch2');
    }

    /**
     * @return array
     */
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/import.msearch2.js',
        );
    }

    public function isHidden()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->tools->hasAddition('msearch2');
    }


    public function getWorker(array $properties = array())
    {
        return null;
    }
}