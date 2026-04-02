<?php

class MsIeResourceExportService extends MsIeExportService
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
        return $this->modx->lexicon('msimportexport_resource_export_service_name');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->modx->lexicon('msimportexport_resource_export_service_description');
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
            $this->config['jsUrl'] . 'mgr/setting/service/export.resource.js',
        );
    }


    /**
     * @return array
     */
    public static function getAllowedFileExtensions()
    {
        $list = array();
        foreach (parent::getAllowedFileExtensions() as $ext) {
            $list[] = $ext;
            if ($ext == 'xlsx') {
                $list[] = 'xlsx2';
            }
        }
        return $list;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $exclude = $this->getExcludeFields();
        return array_merge(
            $this->tools->getResourceFields('', 'Resource', $exclude, true),
            $this->tools->getResourceCustomFields('', 'Resource', $exclude, true),
            $this->tools->getSeoProFields('', 'SeoPro'),
            $this->tools->getTvFields('tv.', 'TV', 'name')
        );
    }

    /**
     * @param array $properties
     * @return MsIeResourceExportWorker|null
     */
    public function getWorker(array $properties = array())
    {
        return $this->loadWorker('MsIeResourceExportWorker', $properties);
    }


}