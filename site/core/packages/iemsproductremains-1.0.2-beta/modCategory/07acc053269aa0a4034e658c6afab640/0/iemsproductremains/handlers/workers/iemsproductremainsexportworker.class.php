<?php
include_once MODX_CORE_PATH . 'components/iems2/handlers/workers/iems2productexportworker.class.php';

class ieMsProductRemainsExportWorker extends MsIeExportWorker
{

    use IeMs2ProductExportWorkerTrait;

    /** @var string $classKey */
    protected $classKey = 'msprRemains';

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = parent::buildQueryConfig();

        $config['innerJoin']['Resource'] = array('class' => 'modResource', 'alias' => 'Resource', 'on' => '`Resource`.`id` = `' . $this->classKey . '`.`product_id`');
        $config['leftJoin']['Data'] = array('class' => 'msProductData', 'alias' => 'Data', 'on' => '`Data`.`id` = `Resource`.`id`');
        $config['leftJoin']['Vendor'] = array('class' => 'msVendor', 'alias' => 'Vendor', 'on' => '`Vendor`.`id` = `Data`.`vendor`');

        $config['select'][$this->classKey] = $this->modx->getSelectColumns($this->classKey, $this->classKey, 'mspr_');
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
        return "`{$this->classKey}`.`product_id`";
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

    public function prepareData(array $data)
    {
        $options = $this->modx->getOption('mspr_options', $data, array(), true);
        if ($options) {
            foreach ($options as $key => $val) {
                $data['mspr_' . $key] = $val;
            }
        }
        $data = parent::prepareData($data);
        return $data;
    }
}