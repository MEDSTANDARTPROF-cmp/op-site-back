<?php

class MsIeResourceExportWorker extends MsIeExportWorker
{
    /**
     * @return bool|string
     */
    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $this->addPrepareFieldMethod('href', $this, 'prepareFieldHref');
            $this->addPrepareFieldMethod('parents', $this, 'prepareFieldParents');
            $this->addLanguageTopic('resource');
        }
        return $initialized;
    }

    /**
     * @return array
     */
    public function buildQueryConfig()
    {

        $config = parent::buildQueryConfig();

        if ($this->tools->hasAddition('seopro')) {
            $config['leftJoin']['SeoPro'] = array('class' => 'seoKeywords', 'alias' => 'SeoPro', 'on' => '`' . $this->classKey . '`.`id` = `SeoPro`.`resource`');
            $config['select']['SeoPro'] = $this->modx->getSelectColumns('seoKeywords', 'SeoPro', 'seo_', array('keywords'), false);
        }
        $fields = $this->getFields();
        if (in_array('parent_pagetitle', $fields)) {
            $config['leftJoin']['Parent'] = array('class' => 'modResource', 'alias' => 'Parent', 'on' => '`' . $this->classKey . '`.`parent` = `Parent`.`id`');
            $config['select']['Parent'] = $this->modx->getSelectColumns('modResource', 'Parent', 'parent_', array('pagetitle'), false);
        }

        $tvs = $this->findTvFields();
        $config['where'] = $this->prepareTvWhere($config['where'], $tvs);

        if ($tvs) {
            $config['includeTVs'] = $tvs;
        }

        if ($resources = $this->getResourceIds()) {
            $config['where']["`{$this->classKey}`.`id`:IN"] = $resources;

        }

        if ($ctx = $this->getSetting('ctx')) {
            $config['where']['context_key'] = $ctx;
        }

        if ($this->getSetting('published_only') == 1) {
            $config['where']['published'] = 1;
        }

        if ($this->getSetting('exclude_deleted') == 1) {
            $config['where']['deleted'] = 0;
        }

        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function prepareQueryConfigWhereByClassKey(array $config = array())
    {
        if ($this->classKey === 'modResource') {
            $config['where']['class_key:IN'] = array('modDocument', 'modSymLink', 'modStaticResource', 'modWebLink');
        } else {
            $config['where']['class_key'] = $this->classKey;
        }
        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function prepareQueryConfig(array $config = array())
    {
        $config = parent::prepareQueryConfig($config);
        $config = $this->prepareQueryConfigWhereByClassKey($config);
        $config['includeTVs'] = is_array($config['includeTVs']) ? $this->tools->cleanAndImplode($config['includeTVs']) : $config['includeTVs'];
        return $config;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return $this->classKey . '.parent';
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldHref($field, array $data, array $result, MsIeWorker &$worker)
    {

        $params = array();
        $utm = '';
        if ($this->getSetting('utm_source')) {
            $params['utm_source'] = $this->getSetting('utm_source');
        }
        if ($this->getSetting('utm_medium')) {
            $params['utm_medium'] = $this->getSetting('utm_medium');
        }
        if ($this->getSetting('utm_campaign')) {
            $params['utm_campaign'] = $this->getSetting('utm_campaign');
        }

        if (!empty($params)) {
            $utm = http_build_query($params);
        }

        if ($this->getSetting('utm_extra_param')) {
            $extra = $this->getSetting('utm_extra_param');
            $extra = $this->tools->getPdoTools()->getChunk('@INLINE ' . $extra, $data);
            $utm = $utm ? "{$utm}&{$extra}" : $extra;

        }

        $url = $this->tools->getSiteUrl(true) . $data['uri'];
        if ($utm) $url .= "?{$utm}";

        $result[$field] = $url;

        return $result;
    }

    /**
     * @param string $field
     * @param array $data
     * @param array $result
     * @param MsIeWorker $worker
     * @return array
     */
    public function prepareFieldParents($field, array $data, array $result, MsIeWorker &$worker)
    {
        $delimiter = $this->getSetting('first_delimiter', '|');
        $result[$field] = $this->getResourceTitleParents($data['id'], $delimiter);
        return $result;
    }

    /**
     * @param array $where
     * @param array $tvs
     * @return array
     */
    public function prepareTvWhere(array $where = array(), array &$tvs = array())
    {
        if (!empty($where['where'])) {
            foreach ($where['where'] as $key => $val) {
                if (preg_match('/^tv\.([^\.][\w\.:=<>!]+)$/', $key)) {
                    $tvs[] = str_replace(array('tv.', ':=', ':!=', ':>', ':<'), '', $key);
                    $where['where'][str_replace(array('tv.'), '', $key)] = $val;
                    unset($where['where'][$key]);
                }
            }
        }
        return $where;
    }

    /**
     * @param int $id
     * @param string $delimiter
     * @return string
     */
    public function getResourceTitleParents($id, $delimiter = '|')
    {
        $result = '';
        $storeKey = 'res_title_parents';
        $ctx = $this->getSetting('ctx', 'web');
        $depth = $this->getSetting('search_depth', 10);
        if ($ids = $this->modx->getParentIds($id, $depth, array('context' => $ctx))) {
            $classKey = 'modResource';
            $key = $ids[0];
            $result = $this->storage->getStoreVal($storeKey, $key);
            if ($result === null) {
                $ids = array_reverse($ids);
                $q = $this->modx->newQuery($classKey);
                $q->select($this->modx->getSelectColumns($classKey, $classKey, '', array('pagetitle')));
                $q->sortby("FIELD({$classKey}.id, {$this->tools->cleanAndImplode($ids)})");
                $q->where(array('id:IN' => $ids));
                if ($q->prepare() && $q->stmt->execute()) {
                    $result = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
                    $result = $this->tools->cleanAndImplode($result, $delimiter);
                    $this->storage->pushStore($storeKey, $result, $key);
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function prepareSkipConverFieldToJson()
    {
        $result = array();
        if ($fields = $this->getSetting('skip_conver_field_to_json', '')) {
            $result = $this->tools->explodeAndClean($fields);
        }
        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareFieldKeys(array $data)
    {
        if ($data) {
            $tvs = $this->tools->getTvFields('tv.', '', 'name');
            foreach ($data as &$field) {
                if (isset($tvs[$field])) {
                    $field = 'tv' . $tvs[$field]['id'];
                } else if ($field == 'parents') {
                    $field = 'parent';
                }
            }
        }
        return $data;
    }
}