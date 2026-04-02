<?php

class ieMsSalePriceTools extends MsIeTools
{

    public function __construct(Msie &$msie, $config = array())
    {
        parent::__construct($msie, $config);
        $this->getMsSalePriceInstance();
    }

    /**
     * @param string $ctx
     * @param array $config
     * @return  msSalePrice|null
     */
    public function getMsSalePriceInstance($ctx = '', $config = array())
    {
        if (!$this->hasAddition('mssaleprice')) return null;
        $ctx = $ctx ? $ctx : $this->modx->context->key;
        if (!isset($this->instances['msSalePrice']) || !is_object($this->instances['msSalePrice'])) {
            $this->instances['msSalePrice'] = $this->modx->getService('mssaleprice', 'msSalePrice', $this->modx->getOption('mssaleprice_core_path', null, $this->modx->getOption('core_path') . 'components/mssaleprice/') . 'model/mssaleprice/');
            $this->instances['msSalePrice']->initialize($ctx, $config);
        }

        return empty($this->instances['msSalePrice']) ? null : $this->instances['msSalePrice'];
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getSalePriceFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        if (!$this->hasAddition('mssaleprice')) return $list;
        $this->getMsSalePriceInstance();
        $aFields = array_keys($this->modx->getFields('msspPrice'));
        $aFields = array_diff($aFields, array('rid', 'id'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'mssaleprice_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'mssaleprice_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'mssaleprice_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getSalePriceCustomFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $aFields = array('prices');

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'msie_alias_'),
                    'label' => $label,

                );
            }
        } else {
            foreach ($aFields as $field) {
                $key = $prefixKey . $field;
                if ($exclude && in_array($field, $fields)) {
                    continue;
                } elseif (empty ($fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msie_alias_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }

    /**
     * @param array $fields
     * @return bool
     */
    public function hasSalePriceFields(array $fields)
    {
        $result = false;
        foreach ($fields as $key => $val) {
            if (preg_match('/^mssp_\w+$/', $val)) {
                return true;
            }
        }
        return $result;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldPrices($field, array $data, array $result, MsIeWorker &$worker)
    {
        $result[$field] = '';
        $count = preg_replace('/[^0-9]/', '', $field);
        $this->getMsSalePriceInstance()->config['json_response'] = false;
        $output = $this->getMsSalePriceInstance()->getPrice($data['id'], $count);
        if ($output['success'] && $output['data']['price']) {
            $result[$field] = $worker->formatPrice($output['data']['price']);
        }
        return $result;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getCountSalePrice($ids = array())
    {
        $result = array();
        $classKey = 'msspPrice';

        $q = $this->modx->newQuery($classKey);
        $q->leftJoin('msProduct', 'Product', '`msspPrice`.`rid` = `Product`.`id`');
        $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('count')));

        $q->where(array(
            'Product.published' => 1,
            'Product.deleted' => 0,
        ));
        if ($ids) {
            $q->where(array(
                'rid:IN' => $ids,
            ));
        }
        $q->sortby('count', 'ASC');
        $q->groupby('count');

        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $result;
    }

}