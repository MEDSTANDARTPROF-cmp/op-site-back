<?php
require_once MODX_CORE_PATH . 'components/msimportexport/model/msimportexport/msie.class.php';
class msImportExportCreateProcessor extends modObjectCreateProcessor {
    /** @var Msie $msie */
    private $msie;
    public $languageTopics = array('msimportexport:default');
    public $checkListPermission = true;
    public function initialize() {
        $this->msie = new Msie($this->modx);
        return true;
    }
    public function beforeSet() {
        $name = trim($this->getProperty('name',''));
        $unit = trim($this->getProperty('unit',''));
        if(empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('msimportexport.err_ns'));
        }

        $this->setProperty('name',$name);
        $this->setProperty('unit',$unit);
        return !$this->hasErrors();
    }
    public function cleanup() {

    }
    public function process() {
        $canSave = $this->beforeSet();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }
        $params = $this->msie->strOption2Arr('msimportexport.export.ym.param_fields');
        $params[$this->getProperty('name')] = $this->getProperty('unit');
        $this->msie->setOption('msimportexport.export.ym.param_fields', $this->msie->arrOption2Str($params));
        return $this->success('',$this->getProperties());
    }

}
return 'msImportExportCreateProcessor';