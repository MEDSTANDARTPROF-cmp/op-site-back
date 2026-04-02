<?php

class IeMs2LinksExportWorker extends MsIeExportWorker
{
    /** @var string $classKey */
    protected $classKey = 'msProductLink';

    /**
     * @return array
     */
    public function buildQueryConfig()
    {
        $config = parent::buildQueryConfig();
        $config['leftJoin']['ProductMaster'] = array('class' => 'msProduct', 'alias' => 'ProductMaster', 'on' => '`' . $this->classKey . '`.`master` = `ProductMaster`.`id`');
        $config['leftJoin']['DataMaster'] = array('class' => 'msProductData', 'alias' => 'DataMaster', 'on' => '`DataMaster`.`id` = `ProductMaster`.`id`');
        $config['leftJoin']['ProductSlave'] = array('class' => 'msProduct', 'alias' => 'ProductSlave', 'on' => '`' . $this->classKey . '`.`slave` = `ProductSlave`.`id`');
        $config['leftJoin']['DataSlave'] = array('class' => 'msProductData', 'alias' => 'DataSlave', 'on' => '`DataSlave`.`id` = `ProductSlave`.`id`');

        $config['select']['ProductMaster'] = $this->modx->getSelectColumns('msProduct', 'ProductMaster', 'master_');
        $config['select']['DataMaster'] = $this->modx->getSelectColumns('msProductData', 'DataMaster', 'master_', array('id'), true);
        $config['select']['ProductSlave'] = $this->modx->getSelectColumns('msProduct', 'ProductSlave', 'slave_');
        $config['select']['DataSlave'] = $this->modx->getSelectColumns('msProductData', 'DataSlave', 'slave_', array('id'), true);

        $config['where']['`ProductMaster`.`class_key`'] = 'msProduct';

        if ($resources = $this->getResourceIds()) {
            $config['where']['`ProductMaster`.`id`:IN'] = $resources;

        }

        if ($ctx = $this->getSetting('ctx')) {
            $config['where']['`ProductMaster`.`context_key`'] = $ctx;
        }

        return $config;
    }


    /**
     * @return string
     */
    public function getSortBy()
    {
        return 'ProductMaster.id';
    }

    /**
     * @return string
     */
    public function getGroupBy()
    {
        return '';
    }

}