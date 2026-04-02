<?php

class ieMs2Tools extends MsIeTools
{
    
    /* @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getProductLinksFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();

        if (!$this->hasAddition('minishop2')) return $list;

        $this->modx->lexicon->load('minishop2:product');
        $aFields = array_keys($this->modx->getFields('msProductLink'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $field,// $this->lexicon($field, 'ms2_product_'),
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
                        'alias' => $field,// $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $field,// $this->lexicon($field, 'ms2_product_'),
                        'label' => $label,
                    );
                } else {
                    continue;
                }
            }
        }
        return $list;
    }
}