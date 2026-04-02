<?php

class ieMsOptionsColorTools extends MsIeTools
{

    /**
     * @return  msoptionscolor|null
     */
    public function getMsOptionsColorInstance()
    {
        if (!$this->hasAddition('msoptionscolor')) return null;
        if (!isset($this->instances['msoptionscolor']) || !is_object($this->instances['msoptionscolor'])) {
            $this->instances['msoptionscolor'] = $this->modx->getService('msoptionscolor', 'msoptionscolor');
        }
        return empty($this->instances['msoptionscolor']) ? null : $this->instances['msoptionscolor'];
    }

    /**
     * @param string $prefixKey
     * @param string $label
     * @param array $fields
     * @param bool $exclude
     * @return array
     */
    public function getOptionsColorFields($prefixKey = '', $label = '', $fields = array(), $exclude = false)
    {
        $list = array();
        if (!$this->hasAddition('msoptionscolor')) return $list;
        $this->modx->lexicon->load('msoptionscolor:manager');
        $aFields = array_keys($this->modx->getFields('msocColor'));

        if (!$exclude && !empty($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $aFields)) {
                    continue;
                }
                $key = $prefixKey . $field;
                $list[$key] = array(
                    'key' => $key,
                    'name' => $field,
                    'alias' => $this->lexicon($field, 'msoptionscolor_'),
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
                        'alias' => $this->lexicon($field, 'msoptionscolor_'),
                        'label' => $label,
                    );
                } elseif ($exclude || in_array($field, $fields)) {
                    $list[$key] = array(
                        'key' => $key,
                        'name' => $field,
                        'alias' => $this->lexicon($field, 'msoptionscolor_'),
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