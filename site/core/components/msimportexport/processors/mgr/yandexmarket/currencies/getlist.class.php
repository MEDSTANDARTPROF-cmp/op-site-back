<?php

class msImportExportYmGetListProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array();
    public $checkListPermission = true;

    public function iterate(array $data = array())
    {
        $list = array();
        $data = array_map('trim', explode(',', $this->modx->getOption('msimportexport.export.ym.currencies', '')));
        foreach ($data as $key) {
            $list[] = array('name' => $key, 'val' => $key);
        }
        return $list;
    }

    public function process()
    {
        $list = $this->iterate();
        $total = count($list);
        return $this->outputArray($list, $total);
    }


}

return 'msImportExportYmGetListProcessor';