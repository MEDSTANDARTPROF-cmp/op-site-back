<?php

class MsIeResourceUpdateWorker extends MsIeResourceImportWorker
{
    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->action = 'update';
            $this->stats = array('errors' => 0, 'updated' => 0);
        }
        return $initialized;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $result = array();
        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(), 1));
        }
        foreach ($this->getFields() as $index => $field) {
            $val = $data[$index];
            if (!empty($this->textFormatFields) && in_array($field, $this->textFormatFields)) {
                $result[$field] = $this->tools->formatText($val, $this->textFormatMethod);
            } else if ($methods = $this->getPrepareFieldMethods($field)) {
                foreach ($methods as $method => $context) {
                    if (method_exists($context, $method)) {
                        $result = $context->$method($field, $val, $data, $result, $this);
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "'{$method}' method not found for preparing the '{$field}' field");
                    }
                }
            } else {
                $result[$field] = $val;
            }
        }
        return $result;
    }

    /**
     * @param array $data
     * @param string $className
     * @return xPDOQuery
     */
    public function buildQuery(array $data = array(), $className = '')
    {
        $className = empty($className) ? $this->classKey : $className;
        $q = $this->modx->newQuery($className);
        $q->command('UPDATE');

        if ($data) {
            $fieldMeta = $this->getFieldMeta($className);
            foreach ($data as $field => $val) {
                if (!isset($fieldMeta[$field])) continue;
                switch ($fieldMeta[$field]['phptype']) {
                    case 'json':
                    case 'array':
                        break;
                    case 'int':
                    case 'float':
                    case 'tinyint':
                    case 'integer':
                        $q->query['set'][$field] = array(
                            'value' => $this->prepareNumberFieldValue($field, $val),
                            'type' => false,
                        );
                        break;
                    default:
                        $q->query['set'][$field] = array(
                            'value' => $val,
                            'type' => true,
                        );
                }
            }
        }

        return $q;
    }

    /**
     * @param array $data
     * @param modResource $resource
     * @return array
     */
    public function makePoolQuery(array $data = array(), modResource $resource)
    {
        $pool = array();
        if (!empty($data)) {
            $q = $this->buildQuery($data);
            $q->where(array(
                'id' => $resource->get('id')
            ));
            $pool[] = $q;
        }
        return $pool;
    }

    /**
     * @param string $field
     * @param string|int|float $val
     * @return string
     */
    public function prepareNumberFieldValue($field, $val)
    {
        if ($val == '') return 0;
        $act = '';
        $val = trim($val);
        $val = str_replace(',', '.', $val);
        $pref = mb_substr($val, 0, 2);
        switch ($pref) {
            case '+=':
                $act = ' + ';
                break;
            case '-=':
                $act = ' - ';
                break;
            case '*=':
                $act = ' * ';
                break;
        }
        return empty($act) ? $val : $this->modx->escape($field) . $act . mb_substr($val, 2);
    }

    /**
     * @param array $data
     */
    public function work(array $data = array())
    {

        if (empty($data)) {
            return;
        }

        $tvs = array();
        $key = $this->getSetting('checking_field', '');
        $value = isset($data[$key]) ? $data[$key] : '';
        $globalCheckExistence = $this->modx->getOption('global_duplicate_uri_check', null, false);
        if (empty($value) || !$resource = $this->findResource($key, $value, $this->classKey, $this->getSettings(), $globalCheckExistence)) {
            $this->incrStatsRecord('errors');
            $err = $this->modx->lexicon('msimportexport_import_err_resource_nf', array('key' => $key, 'value' => $value));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            return;
        }

        unset($data[$key]);

        if ($this->hasTv) {
            $tvs = $this->findTvFields($data, true);
        }

        $poolQuery = $this->makePoolQuery($data, $resource);

        $response = $this->fireEvent('msieOnBeforeImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'tvs' => $tvs, 'poolQuery' => $poolQuery));
        if (!is_array($response)) {
            if ($response === false) {
                $this->incrStatsRecord('errors');
            }
            return;
        }

        $poolQuery = $response['poolQuery'];
        $tvs = $response['tvs'];

        if (!empty($poolQuery)) {
            foreach ($poolQuery as $q) {
                $q->prepare();
                if ($this->debug) {
                    $record = print_r($this->getReadRecord(), 1);
                    $this->debug("Import before run simple update.\n\naction: {$this->action}\nfile record: {$record}\nparams: " . print_r($data, 1) . "\ntvs:" . print_r($tvs, 1) . ' SQL:' . $q->toSQL());
                }

                if (!$q->stmt->execute()) {
                    $this->incrStatsRecord('errors');
                    $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($q->stmt->errorInfo(), true) . ' SQL: ' . $q->toSQL());
                    return;
                }
            }
        }

        if (!empty($tvs)) {
            foreach ($tvs as $tvId => $tvValue) {
                if (!$this->tools->updateTv($resource->get('id'), $tvId, $tvValue)) {
                    unset($tvs[$tvId]);
                    $this->modx->log(modX::LOG_LEVEL_ERROR, "Error update TV resource ID: {$resource->get('id')}; tv ID: {$tvId}; tv Value: {$tvValue}");
                }
            }
        }

        $object = array();//$resource->toArray();

        if ($this->debug) {
            $this->debug("Import after run simple update.\n\nobject: " . print_r($object, 1));
        }

        $this->incrStatsRecord($this->action . 'd');
        $response = $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'tvs' => $tvs, 'object' => $object));

        if (!is_array($response)) {
            return;
        }

        $this->storage->pushStore('ids', $resource->get('id'), $resource->get('id'));
    }

    /**
     * @param string $className
     * @return array
     */
    public function getFieldMeta($className)
    {
        return $this->modx->getFieldMeta($className);
    }
}