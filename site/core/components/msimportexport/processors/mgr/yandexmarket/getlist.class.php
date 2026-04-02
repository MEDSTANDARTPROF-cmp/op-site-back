<?php
require_once MODX_CORE_PATH . 'components/msimportexport/model/msimportexport/msie.class.php';
class msImportExportGetListProcessor extends modObjectGetListProcessor {
    public $languageTopics = array('msimportexport:default');
    public $checkListPermission = true;
    /** @var Msie $msie */
    private $msie;
    public function initialize() {
        $this->msie = new Msie($this->modx);
        return parent::initialize();
    }
    public function iterate(array $data) {
        $list = array();
        foreach ($data as $k => $v) {
            $list[] = array(
                'name' => $k
                ,'unit' => $v
            );
        }
        return $list;
    }
    public function process() {
        $data =  $this->msie->strOption2Arr('msimportexport.export.ym.param_fields');
        $total = count($data);
        $list = $this->iterate($data);
        return $this->outputArray($list,$total);
    }

}
return 'msImportExportGetListProcessor';