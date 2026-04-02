<?php

class msImportExportContextGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modContext';
    public $languageTopics = array('context');
    public $defaultSortField = 'key';

    public function initialize()
    {
        $initialized = parent::initialize();
        $this->setDefaultProperties(array(
            'search' => '',
            'exclude' => 'mgr',
        ));
        return $initialized;
    }

    public function beforeQuery()
    {
        if ($this->getProperty('combo')) {
            $this->setProperty('limit', 0);
        }
        return parent::beforeQuery();
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $search = $this->getProperty('search');
        if (!empty($search)) {
            $c->where(array(
                'key:LIKE' => '%' . $search . '%',
                'OR:description:LIKE' => '%' . $search . '%',
            ));
        }
        $exclude = $this->getProperty('exclude');
        if (!empty($exclude)) {
            $c->where(array(
                'key:NOT IN' => is_string($exclude) ? explode(',', $exclude) : $exclude,
            ));
        }
        return $c;
    }
}

return 'msImportExportContextGetListProcessor';
