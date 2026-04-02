<?php
include_once MODX_CORE_PATH . 'components/iems2/handlers/workers/iems2productexportworker.class.php';

class ieMsSalePriceExportWorker extends MsIeExportWorker
{
    use IeMs2ProductExportWorkerTrait;

    /** @var string $classKey */
    protected $classKey = 'msspPrice';

    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->addPrepareFieldMethod('mssp_type', $this, 'prepareFieldType');
            $this->addPrepareFieldMethod('mssp_price', $this, 'prepareFieldPrice');
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
        $config['leftJoin']['Vendor'] = array('class' => 'msVendor', 'alias' => 'Vendor', 'on' => '`Vendor`.`id` = `Data`.`vendor`');

        $config['select'][$this->classKey] = $this->modx->getSelectColumns($this->classKey, $this->classKey, 'mssp_');
        $config['select']['Resource'] = $this->modx->getSelectColumns('modResource', 'Resource', '');
        $config['select']['Data'] = $this->modx->getSelectColumns('msProductData', 'Data', '', array('id'), true);
        $config['select']['Vendor'] = $this->modx->getSelectColumns('msVendor', 'Vendor', 'vendor_', array('id'), true);

        if ($resources = $this->getResourceIds()) {
            $config['where']['`Resource`.`id`:IN'] = $resources;

        }

        if ($this->getVendorIds()) {
            $config['where']["`Data`.`vendor`:IN"] = $this->getVendorIds();
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
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldPrice($field, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = $this->formatPrice($data[$field]);
        return $result;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldType($field, array $data, array $result, MsIeWorker &$worker)
    {
        $val = $data[$field];
        switch ($val) {
            case 1:
                $val = '=';
                break;
            case 2:
                $val = '+';
                break;
            case 3:
                $val = '-';
                break;
            case 4:
                $val = '*';
                break;
            case 5:
                $val = '/';
                break;
            case 6:
                $val = '%';
                break;
        }
        $result[$field] = $val;
        return $result;
    }


}