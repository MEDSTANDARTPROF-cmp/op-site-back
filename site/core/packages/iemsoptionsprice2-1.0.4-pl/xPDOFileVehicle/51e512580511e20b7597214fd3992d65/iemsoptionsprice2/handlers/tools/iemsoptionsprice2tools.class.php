<?php

class ieMsOptionsPrice2Tools extends MsIeTools
{

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getOptionsPrice2Fields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        if (!$this->hasAddition('msoptionsprice')) return $list;
        $this->modx->lexicon->load('msoptionsprice:manager');
        $aFields = array_keys($this->modx->getFields('msopModification'));
        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'msoptionsprice_'),
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
                        'alias' => $this->lexicon($field, 'msoptionsprice_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msoptionsprice_'),
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
    public function getOptionsPrice2CustomFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        if (!$this->hasAddition('msoptionsprice')) return $list;

        $aFields = array('color', 'size', 'image_file', 'image_path', 'image_url');

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
    public function hasOptionsPrice2Fields(array $fields)
    {
        $result = false;
        foreach ($fields as $key => $val) {
            if (preg_match('/^msopm_\w+$/', $val)) {
                return true;
            }
        }
        return $result;
    }
}