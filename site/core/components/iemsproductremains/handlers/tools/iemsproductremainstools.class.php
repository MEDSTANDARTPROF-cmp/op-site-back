<?php

class ieMsProductRemainsTools extends MsIeTools
{

    public function __construct(Msie &$msie, $config = array())
    {
        parent::__construct($msie, $config);
        $this->getMsProductRemainsInstance();
    }

    /**
     * @param string $ctx
     * @param array $config
     * @return  msProductRemains|null
     */
    public function getMsProductRemainsInstance($ctx = '', $config = array())
    {
        if (!$this->hasAddition('msproductremains')) return null;
        $ctx = $ctx ? $ctx : $this->modx->context->key;
        if (!isset($this->instances['msProductRemains']) || !is_object($this->instances['msProductRemains'])) {
            $this->instances['msProductRemains'] = $this->modx->getService('msproductremains', 'msProductRemains', $this->modx->getOption('mspr_core_path', null, $this->modx->getOption('core_path') . 'components/msproductremains/') . 'model/msproductremains/');
        }
        return empty($this->instances['msProductRemains']) ? null : $this->instances['msProductRemains'];
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getProductRemainsFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        $this->modx->lexicon->load('msproductremains:manager');
        $uFields = array_map('trim', explode(',', $this->modx->getOption('mspr_options', null, '')));
        $aFields = array_keys($this->modx->getFields('msprRemains'));
        $aFields = array_merge($aFields, $uFields);

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'mspr_'),
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
                        'alias' => $this->lexicon($field, 'mspr_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'mspr_'),
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
    public function hasProductRemainsFields(array $fields)
    {
        $result = false;
        foreach ($fields as $key => $val) {
            if (preg_match('/^mspr_\w+$/', $val)) {
                return true;
            }
        }
        return $result;
    }

}