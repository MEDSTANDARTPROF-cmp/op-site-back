<?php

class MsIeResourceImportWorker extends MsIeImportWorker
{
    protected int $templateDefault;
    protected string $classKey = 'modDocument';
    protected string $defaultFieldPrefix = 'resource';
    protected array $textFormatFields = [];
    protected string $textFormatMethod;
    protected bool $hasTv = false;
    protected bool $disableMapGeneration;
    protected bool $skipEmptyCheckingField;
    protected bool $msearch2DisableIndexing;

    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->stats = array_merge($this->stats, array('errors' => 0, 'duplication' => 0, 'created' => 0, 'updated' => 0));
            $this->templateDefault = $this->modx->getOption('default_template', null, 0, true);
            $this->textFormatMethod = $this->getSetting('text_format_method', 'nl2br');
            $this->textFormatFields = $this->tools->explodeAndClean($this->getSetting('text_format_fields', ''));
            $this->disableMapGeneration = $this->getSetting('disable_map_generation', false);
            $this->skipEmptyCheckingField = $this->getSetting('skip_empty_checking_field', false);
            $this->msearch2DisableIndexing = $this->getSetting('msearch2_disable_indexing', false);
            $this->setSetting('parent_class_key', $this->parentClassKey);
            $this->hasTv = $this->hasTvFields($this->getFields());
        }
        return $initialized;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $this->action = 'create';
        $checkingValue = '';
        $checkingField = $this->getSetting('checking_field', '');
        $result = array(
            'msie_fire_event' => 1,
            'mse2_index_disable' => $this->msearch2DisableIndexing,
            'tvs' => $this->hasTv
        );

        if ($this->debug) {
            $this->debug("List fields: \n" . print_r($this->getFields(), 1));
        }

        foreach ($this->getFields() as $index => $field) {
            $val = $data[$index];
            if ($checkingField == $field) {
                $checkingValue = $val;
            }

            if (!empty($this->textFormatFields) && in_array($field, $this->textFormatFields)) {
                $val = $this->tools->formatText($val, $this->textFormatMethod);
            }

            if ($methods = $this->getPrepareFieldMethods($field)) {
                foreach ($methods as $method => $context) {
                    if (method_exists($context, $method)) {
                        $result = $context->$method($field, $val, $data, $result, $this);
                    } else {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "'{$method}' method not found for preparing the '{$field}' field");
                    }
                }
            } else if ($this->isFieldArrayType($field)) {
                $result[$field] = $this->prepareArrayField($val, $field, $result);
            } else {
                $result[$field] = $val;
            }
        }

        $settings = $this->getSettings();
        $settings['ctx'] = $this->modx->getOption('context_key', $result, '', true);

        if (isset($result['parent'])) {
            $result['parent'] = $this->prepareResourceParent($result['parent'], $settings);
        }

        if ($this->skipEmptyCheckingField && empty($checkingValue)) {
            $response = $this->fireEvent('msieOnImportSkipEmptyCheckingField', array('skip' => true, 'fields' => $this->getFields(), 'data' => $data));
            if ($response === null) {
                return [];
            }
        }

        $result = $this->checkExistenceResource($result, $checkingField, $checkingValue, $settings);
        $result = $this->setFieldDefaults($result);
        return $result;
    }


    /**
     * @param array $data
     * @param $checkingField
     * @param $checkingValue
     * @param array $settings
     * @return array
     */
    public function checkExistenceResource(array $data, $checkingField, $checkingValue, array $settings = [])
    {
        $checkExistence = $this->getSetting('check_existence', 1);
        $globalCheckExistence = $this->modx->getOption('global_duplicate_uri_check', null, false);
        if ($checkExistence && $resource = $this->findResource($checkingField, $checkingValue, $this->classKey, $settings, $globalCheckExistence)) {
            $this->action = 'update';
            if (!isset($data['pagetitle']) || empty($data['pagetitle'])) {
                $data['pagetitle'] = $resource->get('pagetitle');
            }

            if (!isset($data['parent'])) {
                $data['parent'] = $resource->get('parent');
            }

            if (!isset($data['alias'])) {
                $data['alias'] = $resource->get('alias');
            }
            if (!isset($data['uri'])) {
                $data['uri'] = $resource->get('uri');
            }

            if (!isset($data['context_key'])) {
                $data['context_key'] = $resource->get('context_key');
            }

            if (!isset($data['template'])) {
                $data['template'] = (int)$resource->get('template');
            }

            if (!isset($data['published'])) {
                $data['published'] = (int)$resource->get('published');
            }

            if (!isset($data['hidemenu'])) {
                $data['hidemenu'] = (int)$resource->get('hidemenu');
            }

            if (!isset($data['searchable'])) {
                $data['searchable'] = (int)$resource->get('searchable');
            }

            $data['id'] = $resource->get('id');
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function setFieldDefaults(array $data)
    {
        if (empty($data['parent'])) {
            $parentDefault = $this->getSetting('parent_default', '');
            if (!$this->getSetting('skip_empty_parent', 1)) {
                $data['parent'] = $parentDefault;
            }
        }

        /*  if ($this->action == 'create' && $this->getSetting('gallery_class', 'msProductFile') == 'msResourceFile') {
              $mediaSource = isset($data['media_source']) ? $data['media_source'] : $this->modx->getOption('ms2gallery_source_default');
              $data['properties'] = array('ms2gallery' => array('media_source' => $mediaSource));
              $data['media_source'] = $mediaSource;
          }*/

        if (empty($data['class_key'])) {
            $data['class_key'] = $this->classKey;
        }

        if (empty($data['content_type'])) {
            $data['content_type'] = $this->modx->getOption('default_content_type', null, 1);
        }

        if (empty($data['context_key'])) {
            $data['context_key'] = $this->getSetting('ctx', 'web');
        }

        if (!isset($data['template'])) {
            $data['template'] = $this->getSetting('template_' . $this->defaultFieldPrefix . '_default', $this->templateDefault);
        }

        if (!isset($data['published'])) {
            $data['published'] = $this->getSetting('published_' . $this->defaultFieldPrefix . '_default', 0);
        }

        if (!isset($data['hidemenu'])) {
            $data['hidemenu'] = $this->getSetting('hidemenu_' . $this->defaultFieldPrefix . '_default', 1);
        }

        if (!isset($data['searchable'])) {
            $data['searchable'] = $this->getSetting('searchable_' . $this->defaultFieldPrefix . '_default', 1);
        }

        return $data;
    }


    /**
     * @param array $data
     */
    public function work(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        if (
            !empty($this->getSetting('skip_action')) &&
            $this->getSetting('skip_action') == $this->action
        ) {
            return;
        }

        if (!isset($data['parent']) || $data['parent'] === '') {
            if (!$this->getSetting('skip_empty_parent', 1)) {
                $this->incrStatsRecord('errors');
                $err = $this->modx->lexicon('msimportexport_import_err_parent_ns', array('info' => print_r($data, 1)));
                $this->tools->log($err);
            }
            return;
        }

        if (empty($data['pagetitle'])) {
            $err = $this->modx->lexicon('msimportexport_import_err_pagetitle_ns', array('info' => print_r($data, 1)));
            $this->tools->log($err);
            return;
        }

        $alias = $this->prepareAlias($data);

        if ($alias === false) {
            return;
        } else if (!empty($alias)) {
            $data['alias'] = $alias;
        }

        $response = $this->fireEvent('msieOnBeforeImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data));
        if (!is_array($response)) {
            if ($response === false) {
                $this->incrStatsRecord('errors');
            }
            return;
        }

        $data = $response['data'];

        if (isset($data['keywords'])) {
            $_POST['keywords'] = $data['keywords'];
        }


        $data['_disable_map_generation'] = $this->disableMapGeneration;

        if ($this->debug) {
            $record = print_r($this->getReadRecord(), 1);
            $this->debug("Import before run processor.\n\naction: {$this->action}\nfile record: {$record}\nparams: " . print_r($data, 1));
        }
        /** @var modProcessorResponse $response */
        $this->modx->error->reset();
        $response = $this->runProcessor('mgr/resource/' . $this->action, $data);
        if ($response->isError()) {
            $err = $this->modx->lexicon('msimportexport_import_err_import',
                array(
                    'action' => $this->action,
                    'message' => $response->getMessage(),
                    'info' => print_r($data, 1) . "\n" . print_r($response->getAllErrors(), 1)
                )
            );

            $this->tools->log($err);
            $this->incrStatsRecord('errors');

            if ($this->hasErrorDuplicationResource($response)) {
                $this->incrStatsRecord('duplication');
                $this->storage->pushStore('duplication', $this->reader->getOffset(), $this->tools->basename($this->file), true);
            }
            return;
        }

        $object = $response->getObject();

        if ($this->debug) {
            $this->debug("Import after run processor.\n\nobject: " . print_r($object, 1));
        }

        $this->incrStatsRecord($this->action . 'd');
        $response = $this->fireEvent('msieOnImport', array('action' => $this->action, 'record' => $this->getReadRecord(), 'data' => $data, 'object' => $object));

        if (!is_array($response)) {
            return;
        }

        $this->storage->pushStore('ids', $object['id'], $object['id']);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function afterFinish(array $data = [])
    {
        $completionAction = $this->getSetting('completion_action');
        $completionAction = $this->tools->explodeAndClean($completionAction);
        $ids = $this->storage->getStore('ids');
        $settings = $this->getSettings();
        $settings['class_key'] = $this->classKey;

        if ($ids && $this->disableMapGeneration) {
            if ($keys = $this->tools->getKeysContexts()) {
                foreach ($keys as $key) {
                    $this->modx->reloadContext($key);
                }
            }
        }

        if ($completionAction) {
            foreach ($completionAction as $action) {
                switch ($action) {
                    case 'unpublish':
                        $this->unpublishResources($ids, $settings);
                        break;
                    case 'remove':
                        $this->removeResources($ids, $settings);
                        break;
                    case 'clear_bin':
                        $this->clearBin($settings);
                        break;
                    case 'clear_cache':
                        $this->modx->cacheManager->refresh();
                        break;
                }
            }
        }

        return parent::afterFinish(array('ids' => $ids));
    }


    /**
     * @param array $fields
     * @return bool
     */
    public function hasTvFields(array $fields)
    {
        foreach ($fields as $field) {
            if (preg_match('/^tv(\d+)$/', $field)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $fields
     * @param bool $remove
     * @return array
     */
    public function findTvFields(&$fields, $remove = true)
    {
        $result = [];
        foreach ($fields as $key => $val) {
            if (preg_match('/^tv(\d+)$/', $key, $match)) {
                $id = $match[1];
                $result[$id] = $val;
                if ($remove) unset($fields[$key]);
            }
        }
        return $result;
    }

    /**
     * @param int $tvId
     * @param mixed $tvVal
     */
    public function addTvField($tvId, $tvVal)
    {
        $this->tvFields[$tvId] = $tvVal;
    }


    /**
     * @param modProcessorResponse $response
     * @return bool
     */
    public function hasErrorDuplicationResource(modProcessorResponse $response)
    {
        if ($response->hasFieldErrors()) {
            $errors = $response->getFieldErrors();
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    if (in_array($error->field, array('uri', 'alias'))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isFieldArrayType($field)
    {
        return false;
    }

    /**
     * @param array $data
     * @return string|bool
     */
    public function prepareAlias(array $data)
    {
        $alias = $data['alias'] ?? '';
        $checkUniqueAlias = $this->getSetting('check_unique_alias', 0);
        if ($checkUniqueAlias == 1 || $checkUniqueAlias == $this->action) {
            $alias = $data['alias'] ?? $this->createAlias($data['pagetitle']);
            if ($id = $this->checkDuplicationResourceAlias($alias, $data, $this->getSettings())) {
                if ($this->getSetting('create_unique_alias', 0)) {
                    $postfix = microtime(true) * 10000;
                    if ($templateAlias = $this->getSetting('template_unique_alias', '')) {
                        if ($templatePostfix = $this->tools->getPdoTools()->getChunk('@INLINE ' . $templateAlias, $data)) {
                            $postfix = $templatePostfix;
                        }
                    }
                    $alias = $this->createAlias($data['pagetitle'], $postfix);
                } else {
                    $response = $this->fireEvent('msieOnImportNotUnique', array('action' => $this->action, 'field' => 'alias', 'duplicate' => $id, 'record' => $this->getReadRecord(), 'data' => $data));
                    if (!is_array($response)) {
                        if ($response === false) {
                            $this->incrStatsRecord('errors');
                            $this->incrStatsRecord('duplication');
                            $this->storage->pushStore('duplication', $this->reader->getOffset(), $this->tools->basename($this->file), true);
                        }
                        return false;
                    }
                    $data = $response['data'];
                    $alias = $this->modx->getOption('alias', $data, '', true);
                    unset($response);
                }
            }
        }
        return $alias;
    }

    /**
     * @param mixed $val
     * @param string $field
     * @param array $data
     * @return array
     */
    public function prepareArrayField($val, $field, array $data = [])
    {
        $delimiter = $this->getSetting('first_delimiter', '|');
        $val = $this->tools->explodeAndClean($val, $delimiter);
        if (empty($data[$field])) $data[$field] = [];
        $data[$field] = array_merge($data[$field], $val);
        return $data[$field];
    }

    /**
     * @param int|string $parent
     * @param array|null $options
     * @return int
     */
    public function prepareResourceParent($parent, $options = [])
    {
        $id = 0;
        if (is_numeric($parent)) {
            if (empty($parent) || !$this->resourceIdExists($parent)) {
                $parent = 0;
            }
            return $parent;
        } else {
            if (empty($parent)) return $id;
            if (!$id = $this->storage->getStoreVal('parent_id', $parent, 0)) {
                $firstDelimiter = $options['first_delimiter'] ?: '|';
                $parentDelimiter = $options['parent_delimiter'] ?: $firstDelimiter;
                $parentDefault = 0;//$this->modx->getOption('parent_default', $options, 0, true);
                $parents = $this->tools->explodeAndClean($parent, $parentDelimiter);
                $id = $this->createResourceChain($parents, $parentDefault, $options);
                if ($id) {
                    $this->storage->pushStore('parent_id', $id, $parent);
                }
            }
        }
        return $id;
    }


    /**
     * @param string|array $resources
     * @param null|int $parent
     * @param array $options
     * @return int
     */
    public function createResourceChain($resources, $parent = null, $options = [])
    {
        $id = is_numeric($parent) ? $parent : 0;
        if (is_string($resources)) {
            $firstDelimiter = $options['first_delimiter'] ?: '|';
            $parentDelimiter = $options['parent_delimiter'] ?: $firstDelimiter;
            $resources = $this->tools->explodeAndClean($resources, $parentDelimiter);
        }

        foreach ($resources as $key => $resource) {
            if ($rid = $this->getResourceIdByPageTitle($resource, $id, $options)) {
                $id = $rid;
                continue;
            } else {
                $id = $this->createResource($resource, $id, $options);
            }
        }
        return $id;
    }

    /**
     * @param string $pageTitle
     * @param null|int $parent
     * @param array $options
     * @return int
     */
    public function getResourceIdByPageTitle($pageTitle, $parent = null, $options = [])
    {
        $id = 0;
        if (empty($pageTitle)) return $id;
        $pageTitle = trim($pageTitle);
        $ctx = $this->modx->getOption('ctx', $options, '', true);
        $checkAlias = $this->modx->getOption('use_alias_in_search', $options, 0, true);
        $key = md5($pageTitle . $ctx . $checkAlias . $parent);

        if ($id = $this->storage->getStoreVal('resource_id', $key, 0)) {
            return $id;
        }

        $q = $this->modx->newQuery('modResource');

        if ($checkAlias) {
            $q->where(array(
                'pagetitle:=' => $pageTitle,
                'OR:alias:=' => $this->createAlias($pageTitle),
            ));
        } else {
            $q->where(array(
                'pagetitle:=' => $pageTitle,
            ));
        }

        if ($ctx) {
            $q->where(array(
                'context_key:=' => $ctx,
            ));
        }

        if (is_numeric($parent)) {
            $q->where(array(
                'parent:=' => $parent,
            ));
        }

        $q->select('id');

        if ($q->prepare() && $q->stmt->execute()) {
            $id = $q->stmt->fetch(PDO::FETCH_COLUMN);
            $this->storage->pushStore('resource_id', $id, $key);
        }
        return $id;
    }

    /**
     * @param string $pageTitle
     * @param null|int $parent
     * @param array $options
     * @return int
     */
    public function createResource($pageTitle, $parent = null, $options = [])
    {
        $pageTitle = trim($pageTitle);
        $defaultFieldPrefix = $options['defaultFieldPrefix'] ?: $this->defaultFieldPrefix;
        $defaultFieldTemplate = $options['defaultFieldTemplate'] ?: 'default_template';
        $templateDefault = $this->modx->getOption($defaultFieldTemplate, null, 0, true);

        if ($parent === null) {
            $parent = $options['parent_default'] ?: 0;
        }

        if (!$classKey = $this->modx->getOption('parent_class_key', $options, '', true)) {
            $classKey = $options['class_key'] ?: 'modDocument';
        }

        $data = array(
            'msie_fire_event' => 1,
            'mse2_index_disable' => $this->msearch2DisableIndexing,
            'pagetitle' => trim($pageTitle),
            'parent' => $parent,
            'context_key' => $this->modx->getOption('ctx', $options, 'web', true),
            'template' => $this->modx->getOption('template_' . $defaultFieldPrefix . '_default', $options, $templateDefault, true),
            'published' => $this->modx->getOption('published_' . $defaultFieldPrefix . '_default', $options, 1, true),
            'hidemenu' => $this->modx->getOption('hidemenu_' . $defaultFieldPrefix . '_default', $options, 0, true),
            'searchable' => $this->modx->getOption('searchable_' . $defaultFieldPrefix . '_default', $options, 1, true),
            'class_key' => $classKey,
        );

        $response = $this->modx->runProcessor('resource/create', $data);

        if (!$response || $response->isError()) {
            $err = $this->modx->lexicon('msimportexport_import_err_create_resource', array('info' => print_r($response->getAllErrors(), 1)));
            $this->modx->log(modX::LOG_LEVEL_ERROR, $err);
            return 0;
        }

        $resource = $response->getObject();
        return $resource['id'];
    }

    /**
     * @param string $alias
     * @param array $resourceData
     * @param array $options
     *
     * @return int
     */
    public function checkDuplicationResourceAlias($alias, array $resourceData = [], array $options = [])
    {
        $id = 0;
        /**@var modResource $resource */
        if ($resource = $this->modx->newObject('modResource', $resourceData)) {
            $checkingField = $this->getSetting('checking_field', '');
            $ctx = $this->modx->getOption('ctx', $options, '', true);
            $duplicateContext = $this->modx->getOption('global_duplicate_uri_check', false) ? '' : $ctx;
            $aliasPath = $resource->getAliasPath($alias, $resourceData);
            $id = $resource->isDuplicateAlias($aliasPath, $duplicateContext);
            if ($id) {
                $duplicateResource = $this->modx->getObject('modResource', $id);
                if ($duplicateResource->get($checkingField) == $resource->get($checkingField)) {
                    $id = 0;
                }
                unset($duplicateResource);
            }
        }
        unset($resource);
        return $id;
    }

    /**
     * @param string $text
     * @param string $postfix
     *
     * @return string
     */
    public function createAlias($text, $postfix = '')
    {
        $res = $this->modx->newObject('modResource');
        $delimiter = $this->modx->getOption('friendly_alias_word_delimiter', null, '-');
        $alias = $res->cleanAlias($text);
        return empty($postfix) ? $alias : ($alias . $delimiter . trim($postfix));
    }


    /**
     * @param array $ids
     * @param array $options
     * @return bool|void
     */
    public function unpublishResources($ids = [], array $options = [])
    {
        if (empty($ids)) return;
        $ids = $this->tools->cleanAndImplode($ids);
        $ctx = $this->modx->getOption('ctx', $options, '', true);
        $classKey = $this->modx->getOption('class_key', $options, 'modResource', true);

        $sql = "UPDATE {$this->modx->getTableName('modResource')} SET published = 0 WHERE class_key = '{$classKey}' AND id NOT IN ({$ids})";

        if (!empty($ctx)) {
            $sql .= " context_key = '{$ctx}'";
        }

        if ($this->modx->exec($sql)) {
            return true;
        } else {
            $err = $this->modx->pdo->errorInfo();
            switch ($err[0]) {
                case '01000':
                case '00000':
                    return true;
                    break;
                default:
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unpublish Products  error info: ' . print_r($err, 1) . "\nSQL: " . $sql);
                    return false;
            }
        }
    }

    /**
     * @param array $ids
     * @param array $options
     * @return bool|void
     */
    public function removeResources($ids = [], array $options = [])
    {
        $result = true;
        if (empty($ids)) return;
        $ids = $this->tools->cleanAndImplode($ids);
        $ctx = $this->modx->getOption('ctx', $options, '', true);
        $classKey = $this->modx->getOption('class_key', $options, 'modResource', true);
        $sql = "UPDATE {$this->modx->getTableName('modResource')} SET deleted = 1 WHERE class_key = '{$classKey}' AND id NOT IN ({$ids}) ";

        if (!empty($ctx)) {
            $sql .= " context_key = '{$ctx}'";
        }

        if (!$this->modx->exec($sql)) {
            $err = $this->modx->pdo->errorInfo();
            switch ($err[0]) {
                case '01000':
                case '00000':
                    break;
                default:
                    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unpublish Resources  error info: ' . print_r($err, 1) . "\nSQL: " . $sql);
                    $result = false;
            }
        }
        return $result;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function clearBin(array $options = [])
    {
        $result = true;
        $count = $this->modx->getCount('modResource', array('deleted' => 1));
        if ($count) {
            $response = $this->modx->runProcessor('resource/emptyrecyclebin');
            if ($response->isError()) {
                $result = false;
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Emptying the basket error:' . $response->getMessage() . 'Info: ' . print_r($response->getAllErrors(), 1));
            }
        }
        return $result;
    }

}