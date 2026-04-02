<?php
include_once MODX_CORE_PATH . 'components/iems2/handlers/workers/iems2productexportworker.class.php';

class ieMsOptionsPrice2ExportWorker extends MsIeExportWorker
{
    use IeMs2ProductExportWorkerTrait;

    /** @var string $classKey */
    protected $classKey = 'msopModification';

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->addPrepareFieldMethod('msopm_image_path', $this, 'prepareFieldImagePath');
        }
        return $initialized;
    }

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = parent::buildQueryConfig();
        $config['innerJoin']['Resource'] = array('class' => 'modResource', 'alias' => 'Resource', 'on' => '`Resource`.`id` = `' . $this->classKey . '`.`rid`');
        $config['leftJoin']['Data'] = array('class' => 'msProductData', 'alias' => 'Data', 'on' => '`Data`.`id` = `Resource`.`id`');
        $config['leftJoin']['File'] = array('class' => 'msProductFile', 'alias' => 'File', 'on' => '`File`.`id` = `' . $this->classKey . '`.`image`');
        $config['leftJoin']['Vendor'] = array('class' => 'msVendor', 'alias' => 'Vendor', 'on' => '`Vendor`.`id` = `Data`.`vendor`');

        $config['select'][$this->classKey] = $this->modx->getSelectColumns($this->classKey, $this->classKey, 'msopm_');
        $config['select']['File'] = 'CONCAT(`File`.`path`,`File`.`file`) AS msopm_image_path, `File`.`url` AS msopm_image_url, `File`.`source` AS msopm_image_source, `File`.`file` AS msopm_image_file';
        $config['select']['Resource'] = $this->modx->getSelectColumns('modResource', 'Resource', '');
        $config['select']['Data'] = $this->modx->getSelectColumns('msProductData', 'Data', '', array('id'), true);
        $config['select']['Vendor'] = $this->modx->getSelectColumns('msVendor', 'Vendor', 'vendor_', array('id'), true);


        if ($resources = $this->getResourceIds()) {
            $config['where']['`Resource`.`id`:IN'] = $resources;
        }

        if ($this->getVendorIds()) {
            $config['where']["`Data`.`vendor`:IN"] = $this->getVendorIds();
        }


        if ($ctx = $this->getSetting('ctx')) {
            $config['where']['`Resource`.`context_key`'] = $ctx;
        }

        if ($this->getSetting('published_only') == 1) {
            $config['where']['`Resource`.`published`'] = 1;
        }

        if ($this->getSetting('exclude_deleted') == 1) {
            $config['where']['`Resource`.`deleted`'] = 0;
        }


        return $config;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return "`{$this->classKey}`.`rid`";
    }

    /**
     * @return string
     */
    public function getSortDir()
    {
        return 'ASC';
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        return "`{$this->classKey}`.`id`";
    }

    /**
     * @param array $data
     * @return array
     */
    public function beforePrepareData(array $data)
    {
        $options = $this->modx->call('msopModificationOption', 'getOptions', array(&$this->modx, $data['msopm_id'], $data['msopm_rid'], null, false, 'msopm_'));
        $data = array_merge($data, $options);
        return $data;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldImagePath($field, array $data, array $result, MsIeWorker &$worker)
    {
        $source = $this->modx->getOption('msopm_image_source', $data, 1, true);
        $ctx = $this->modx->getOption('context_key', $data, 'web', true);
        $path = $this->tools->getPathByMediaSource($source, $ctx);
        $result[$field] = $path . $data[$field];
        return $result;
    }


    /**
     * @param array $data
     * @return array
     */
    public function prepareFieldKeys(array $data)
    {
        $index = array_search('msopm_image_file', $data);
        if ($index !== false) {
            $data[$index] = 'msopm_image';
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getFieldNames()
    {
        $result = array();
        if ($keys = $this->getFieldKeys()) {
            foreach ($keys as $key) {
                $postfix = '';
                if (strpos($key, 'msopm_') !== false) {
                    $postfix = ' - msOptionsPrice2';
                    $key = str_replace('msopm_', '', $key);
                }
                $key = 'msie_alias_' . $key;
                $result[] = $this->modx->lexicon($key) . $postfix;
            }
        }
        return $result;
    }

}