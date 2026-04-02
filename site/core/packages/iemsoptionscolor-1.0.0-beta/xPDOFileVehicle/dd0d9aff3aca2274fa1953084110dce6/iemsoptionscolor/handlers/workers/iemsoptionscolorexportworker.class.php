<?php
include_once MODX_CORE_PATH . 'components/iems2/handlers/workers/iems2productexportworker.class.php';

class ieMsOptionsColorExportWorker extends MsIeExportWorker
{
    use IeMs2ProductExportWorkerTrait;

    /** @var string $classKey */
    protected $classKey = 'msocColor';

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = parent::buildQueryConfig();
        $config['innerJoin']['Resource'] = array('class' => 'modResource', 'alias' => 'Resource', 'on' => '`Resource`.`id` = `' . $this->classKey . '`.`rid`');
        $config['leftJoin']['Data'] = array('class' => 'msProductData', 'alias' => 'Data', 'on' => '`Data`.`id` = `Resource`.`id`');
        $config['leftJoin']['Vendor'] = array('class' => 'msVendor', 'alias' => 'Vendor', 'on' => '`Vendor`.`id` = `Data`.`vendor`');

        $config['select'][$this->classKey] = $this->modx->getSelectColumns($this->classKey, $this->classKey, 'msoc_');
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
    public function getGroupBy()
    {
        return '';
    }
}