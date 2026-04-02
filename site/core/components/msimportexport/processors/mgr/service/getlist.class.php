<?php

class msImportExportServiceGetListProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function process()
    {
        $result = array();
        $mode = $this->getProperty('mode');
        if ($services = $this->msie->getServices($mode)) {
            foreach ($services as $service) {
                if ($service->isHidden() || !$service->isEnabled()) continue;
                $ext = $service->getAllowedFileExtensions();
                $result[] = array(
                    'key' => $service->getName(),
                    'name' => $service->getTitle(),
                    'description' => $service->getDescription(),
                    'home_link' => $service->getHomeLink(),
                    'extensions' => $this->prepareExtensions($ext),
                );
                unset($worker);
            }
        }

        return $this->outputArray($result, count($result));
    }

    /**
     * @param array $list
     * @return array
     */
    public function prepareExtensions(array $list)
    {
        $result = array();
        if ($list) {
            foreach ($list as $item) {
                $result[] = $this->modx->lexicon('msimportexport_system_file_type_' . $item);
            }
        }
        return $result;
    }
}

return 'msImportExportServiceGetListProcessor';
