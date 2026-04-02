<?php

class  msImportExportPresetExportProcessor extends modProcessor
{
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function getLanguageTopics()
    {
        return array('msimportexport:default');
    }

    /**
     * @return array|string
     */
    public function process()
    {
        /**
         * @var MsIeTools $tools
         * @var MsiePreset $preset
         */
        $tools = $this->msie->getTools();
        $ids = $tools->explodeAndClean($this->getProperty('ids', ''));
        if (empty($ids)) {
            return $this->modx->lexicon('msimportexport_preset_err_ns');
        }

        $classKey = 'MsiePreset';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('id'), true));
        $q->where(array('id:IN' => $ids));

        $result = '';
        if ($q->prepare() && $q->stmt->execute()) {
            if ($result = $q->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $result = $this->modx->toJSON($result);
            }
        }

        if (empty($result)) return;

        $filename = 'presets.json';

        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $result;
    }
}

return 'msImportExportPresetExportProcessor';