<?php

class msImportExportServiceFormatGetListProcessor extends modProcessor
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
        if ($preset = $this->modx->getObject('MsiePreset', $this->getProperty('preset', 0))) {
            if ($service = $this->msie->getService(Msie::MODE_EXPORT, $preset->get('service'))) {
                if ($extensions = $service->getAllowedFileExtensions()) {
                    foreach ($extensions as $val) {
                        $result[] = array(
                            'key' => $val,
                            'name' => $this->modx->lexicon('msimportexport_system_file_type_' . $val),
                        );
                    }
                }
            }
        }

        return $this->outputArray($result, count($result));
    }
}

return 'msImportExportServiceFormatGetListProcessor';
