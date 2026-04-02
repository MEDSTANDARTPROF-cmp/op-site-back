<?php

include_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/product/update.class.php';

class  IeMs2ProductUpdateProcessor extends msProductUpdateProcessor
{

    public $permission = '';

    public static function getInstance(modX &$modx, $className, $properties = array())
    {
        $className = __CLASS__;
        $processor = new $className($modx, $properties);
        return $processor;
    }

    public function checkPermissions()
    {
        return true;
    }

    /**
     * Cleanup the processor and return the resulting object
     *
     * @return array
     */
    public function cleanup()
    {
        $this->object->removeLock();
        //  $this->clearCache();

        $returnArray = $this->object->get(array_diff(array_keys($this->object->_fields), array('content', 'ta', 'introtext', 'description', 'link_attributes', 'pagetitle', 'longtitle', 'menutitle', 'properties')));
        foreach ($returnArray as $k => $v) {
            if (strpos($k, 'tv') === 0) {
                unset($returnArray[$k]);
            }
        }
        $returnArray['class_key'] = $this->object->get('class_key');
        $this->workingContext->prepare(false);

        if ($this->getProperty('_disable_map_generation', false)) {
            $this->modx->reloadContext($this->workingContext->key);
        }

        $returnArray['preview_url'] = '';
        if (!$this->object->get('deleted')) {
            $returnArray['preview_url'] = $this->modx->makeUrl($this->object->get('id'), $this->object->get('context_key'), '', 'full');
        }

        return $this->success('', $returnArray);
    }

    /**
     * Set any Template Variables passed to the Resource. You must pass "tvs" as 1 or true to initiate these checks.
     * @return array|mixed
     */

    public function saveTemplateVariables()
    {
        $tvs = $this->getProperty('tvs', null);
        if (!empty($tvs)) {
            $tmplvars = array();

            $tvs = $this->object->getTemplateVars();
            /** @var modTemplateVar $tv */
            foreach ($tvs as $tv) {
                if (!$tv->checkResourceGroupAccess()) {
                    continue;
                }

                $tvKey = 'tv' . $tv->get('id');
                $value = $this->getProperty($tvKey, null);
                /* set value of TV */
                if ($tv->get('type') != 'checkbox') {
                    $value = $value !== null ? $value : $tv->get('default_text');
                } else {
                    $value = $value ? $value : '';
                }

                /* validation for different types */
                switch ($tv->get('type')) {
                    case 'url':
                        $prefix = $this->getProperty($tvKey . '_prefix', '');
                        if ($prefix != '--') {
                            $value = str_replace(array('ftp://', 'http://'), '', $value);
                            $value = $prefix . $value;
                        }
                        break;
                    case 'date':
                        $value = empty($value) ? '' : strftime('%Y-%m-%d %H:%M:%S', strtotime($value));
                        break;
                    /* ensure tag types trim whitespace from tags */
                    case 'tag':
                    case 'autotag':
                        $tags = explode(',', $value);
                        $newTags = array();
                        foreach ($tags as $tag) {
                            $newTags[] = trim($tag);
                        }
                        $value = implode(',', $newTags);
                        break;
                    default:
                        /* handles checkboxes & multiple selects elements */
                        if (is_array($value)) {
                            $featureInsert = array();
                            foreach ($value as $featureValue => $featureItem) {
                                if (isset($featureItem) && $featureItem === '') {
                                    continue;
                                }
                                $featureInsert[count($featureInsert)] = $featureItem;
                            }
                            $value = implode('||', $featureInsert);
                        }
                        break;
                }

                /* if different than default and set, set TVR record */
                $default = $tv->processBindings($tv->get('default_text'), $this->object->get('id'));
                if (strcmp($value, $default) != 0) {
                    /* update the existing record */
                    $tvc = $this->modx->getObject('modTemplateVarResource', array(
                        'tmplvarid' => $tv->get('id'),
                        'contentid' => $this->object->get('id'),
                    ));
                    if ($tvc == null) {
                        /** @var modTemplateVarResource $tvc add a new record */
                        $tvc = $this->modx->newObject('modTemplateVarResource');
                        $tvc->set('tmplvarid', $tv->get('id'));
                        $tvc->set('contentid', $this->object->get('id'));
                    }
                    $tvc->set('value', $value);
                    $tvc->save();

                    /* if equal to default value, erase TVR record */
                } else {
                    /* $tvc = $this->modx->getObject('modTemplateVarResource',array(
                         'tmplvarid' => $tv->get('id'),
                         'contentid' => $this->object->get('id'),
                     ));
                     if (!empty($tvc)) {
                         $tvc->remove();
                     }*/
                }
            }
        }
        return $tvs;
    }
}

return 'IeMs2ProductUpdateProcessor';
