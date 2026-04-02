<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/getlist.class.php';

class msImportExportResourceGetListProcessor extends modResourceGetListProcessor
{

    public $defaultSortField = 'menuindex';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;
    protected $depth;

    public function initialize()
    {
        if ($ok = parent::initialize()) {
            $this->classKey = $this->getProperty('class_key', 'modDocument');
            $this->msie = $this->modx->getService('msimportexport', 'Msie');
            $this->depth = $this->msie->getTools()->getSysSetting('search_depth', 10);
        }
        return $ok;
    }

    /**
     * Can be used to adjust the query prior to the COUNT statement
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');

        if (!empty($query)) {
            $c->where(array(
                'pagetitle:LIKE' => '%' . $query . '%',
            ));
        }

        $c->where(array(
            'class_key' => $this->classKey
        ));

        return $c;
    }

    public function beforeIteration(array $list)
    {
        if ($this->classKey == 'modDocument') {
            $list[] = array(
                'id' => 0,
                'pagetitle' => $this->modx->lexicon('msimportexport_resource_root'),
                'context_key' => '',
            );
        }
        $list = parent::beforeIteration($list);
        return $list;
    }


    /**
     * Iterate across the data
     *
     * @param array $data
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;

        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $object) {
            if ($this->checkListPermission && $object instanceof modAccessibleObject && !$object->checkPolicy('list')) continue;
            $objectArray = $this->prepareRow($object);
            $objectArray['parents'] = '';
            if (!empty($objectArray) && is_array($objectArray)) {
                $objectArray['parents'] = $this->getParents($objectArray['id'], $objectArray['context_key'], $this->depth);
                $list[] = $objectArray;
                $this->currentIndex++;
            }
        }
        $list = $this->afterIteration($list);
        return $list;
    }

    /**
     * @param int $id
     * @param int $depth
     * @param string $ctx
     * @return string
     */
    public function getParents($id, $ctx = 'web', $depth = 10)
    {
        $parents = '';
        if ($ids = $this->modx->getParentIds($id, $depth, array('context' => $ctx))) {
            $ids = array_reverse($ids);
            $q = $this->modx->newQuery('modResource');
            $q->where(array('id:IN' => $ids));
            $q->select(array('pagetitle'));
            if ($q->prepare() && $q->stmt->execute()) {
                if ($result = $q->stmt->fetchAll(PDO::FETCH_COLUMN, 0)) {
                    $parents = implode('->', $result);
                }
            }
        }

        return $parents;
    }

    /**
     * @param array $exclude
     * @return array
     */
    public function getContexts(array $exclude = array('mgr'))
    {
        $result = array();
        $classKey = 'modContext';
        $q = $this->modx->newQuery($classKey);
        $q->select($this->modx->getSelectColumns($classKey, $classKey));
        if ($exclude) {
            $q->where(array('key:NOT IN' => $exclude));
        }
        if ($q->prepare() && $q->stmt->execute()) {
            $result = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $result;
    }

}

return 'msImportExportResourceGetListProcessor';
