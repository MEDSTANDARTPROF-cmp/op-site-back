<?php
require_once MODX_CORE_PATH . 'components/msimportexport/model/msimportexport/msie.class.php';
class msImportExportUpdateProcessor extends modObjectUpdateProcessor {
    public $languageTopics = array('msimportexport:default');
    public $checkListPermission = true;
    /** @var Msie $msie */
    private $msie;
    public function initialize() {
        $this->msie = new Msie($this->modx);
        return true;
    }

    public function beforeSet() {
        $name = trim($this->getProperty('name',''));
        $unit = trim($this->getProperty('unit',''));
        $this->setProperty('name',$name);
        $this->setProperty('unit',$unit);
        return !$this->hasErrors();
    }

    /**
     * {@inheritDoc}
     * @return mixed
     */
    public function process() {
        $canSave = $this->beforeSet();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }
        $name = $this->getProperty('name');
        if($params = $this->msie->strOption2Arr('msimportexport.export.ym.param_fields')) {
            $params[$name] = $this->getProperty('unit');
            $this->msie->setOption('msimportexport.export.ym.param_fields', $this->msie->arrOption2Str($params));
        }
        return $this->success('',$this->getProperties());
    }


}
return 'msImportExportUpdateProcessor';