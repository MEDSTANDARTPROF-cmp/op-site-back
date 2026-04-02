<?php

class MsIeResourceUpdateService extends MsIeImportService
{

    /** @var int $rank */
    protected $rank = 1;

    public function initialize()
    {
        $this->modx->lexicon->load('msimportexport:service_resource');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('msimportexport_resource_update_service_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('msimportexport_resource_update_service_description');
    }

    /**
     * @return string
     */
    public function getHomeLink()
    {
        return 'https://modstore.pro/packages/import-and-export/msimportexport';
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('msimportexport:service_resource');
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Resource', $exclude, true),
            $this->tools->getTvFields('tv', 'TV')
        );
    }

    /**
     * @return array
     */
    public function getCheckingFields()
    {
        $exclude = $this->getExcludeFields();
        return $this->tools->getResourceFields('', '', $exclude, true);
    }

    /**
     * @param array $properties
     * @return MsIeResourceUpdateWorker|null
     */
    public function getWorker(array $properties = array())
    {
        return $this->loadWorker('MsIeResourceUpdateWorker', $properties);
    }

}