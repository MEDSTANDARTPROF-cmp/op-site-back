<?php

class MsIeResourceImportService extends MsIeImportService
{

    public function initialize()
    {
        $this->modx->lexicon->load('msimportexport:service_resource');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->modx->lexicon('msimportexport_resource_import_service_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('msimportexport_resource_import_service_description');
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
    public function getJavaScripts()
    {
        return array(
            $this->config['jsUrl'] . 'mgr/setting/service/import.resource.js',
        );
    }


    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Resource', $exclude, true),
            $this->tools->getSeoProFields('', 'SeoPro'),
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
     * @return MsIeResourceImportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        return $this->loadWorker('MsIeResourceImportWorker', $properties);
    }

}