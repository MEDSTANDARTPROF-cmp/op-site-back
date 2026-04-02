<?php
require_once MODX_CORE_PATH . 'components/msimportexport/model/msimportexport/msie.class.php';
class msImportExportRemoveProcessor extends modObjectRemoveProcessor {
    /** @var Msie $msie */
    private $msie;
    public $languageTopics = array('msimportexport:default');
    public function initialize() {
        $this->msie = new Msie($this->modx);
        return true;
    }

    public function process() {
        $name = $this->getProperty('name');
        if (!empty($name)) {
            if($params = $this->msie->strOption2Arr('msimportexport.export.ym.param_fields')) {
                if (isset($params[$name])) {
                    unset($params[$name]);
                    $this->msie->setOption('msimportexport.export.ym.param_fields', $this->msie->arrOption2Str($params));
                }
            }
        }
        return $this->success('',array());
    }


}
return 'msImportExportRemoveProcessor';