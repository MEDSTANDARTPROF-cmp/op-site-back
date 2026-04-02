<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/getnodes.class.php';

class msImportExportResourceGetNodesProcessor extends modResourceGetNodesProcessor
{
    protected $pid;
    protected $parent_id;
    protected $preset;
    protected $resources = array();
    /** @var Msie $msie */
    protected $msie;

    /** {@inheritDoc} */
    public function initialize()
    {
        $initialize = parent::initialize();
        $this->msie = $this->modx->getService('msimportexport', 'Msie');
        $this->itemClass = $this->getProperty('class_key', 'modDocument');
        $this->pid = $this->getProperty('currentResource');
        $presetId = $this->getProperty('preset', 0);
        if ($presetId) {
            /** @var MsiePreset $preset */
            if ($preset = $this->modx->getObject('MsiePreset', $presetId)) {
                $resources = $preset->getSetting('resources', '');
                $this->resources = $this->msie->getTools()->explodeAndClean($resources);
            }
        }

        if ($res = $this->modx->getObject($this->itemClass, $this->pid)) {
            $this->parent_id = $res->get('parent');
        }

        return $initialize;
    }


    /** {@inheritDoc} */
    public function getResourceQuery()
    {
        $resourceColumns = array(
            'id',
            'pagetitle',
            'longtitle',
            'alias',
            'description',
            'parent',
            'published',
            'deleted',
            'isfolder',
            'menuindex',
            'menutitle',
            'hidemenu',
            'class_key',
            'context_key',
        );
        $c = $this->modx->newQuery('modResource');
        $c->leftJoin('modResource', 'Child', array('modResource.id = Child.parent'));
        $c->select($this->modx->getSelectColumns('modResource', 'modResource', '', $resourceColumns));
        $c->select(array(
            'childrenCount' => 'COUNT(Child.id)'
        ));

        if ($this->itemClass == 'msCategory') {
            $c->leftJoin('msCategoryMember', 'Member', array('modResource.id = Member.category_id AND Member.product_id = ' . $this->pid));
            $c->select(array(
                'member' => 'category_id'
            ));
           /* $c->where(array(
                'isfolder' => true,
            ));*/
        }


        $c->where(array(
            'context_key' => $this->contextKey,
            'show_in_tree' => true,
        ));

        if ($this->itemClass !== 'modResource') {
            $c->where(array(
                'class_key' => $this->itemClass,
            ));
        }

        if (empty($this->startNode) && !empty($this->defaultRootId)) {
            $c->where(array(
                'id:IN' => explode(',', $this->defaultRootId),
                'parent:NOT IN' => explode(',', $this->defaultRootId),
            ));
        } else {
            $c->where(array(
                'parent' => $this->startNode,
            ));
        }
        $c->groupby($this->modx->getSelectColumns('modResource', 'modResource', '', $resourceColumns), '');
        $c->sortby('modResource.' . $this->getProperty('sortBy'), $this->getProperty('sortDir'));

        return $c;
    }


    /** {@inheritDoc} */
    public function prepareContextNode(modContext $context)
    {
        $context->prepare();
        return array(
            'text' => $context->get('key')
        , 'id' => $context->get('key') . '_0'
        , 'pk' => $context->get('key')
        , 'ctx' => $context->get('key')
        , 'leaf' => false
        , 'cls' => 'icon-context'
        , 'iconCls' => $this->modx->getOption('mgr_tree_icon_context', null, 'tree-context')
        , 'qtip' => $context->get('description') != '' ? strip_tags($context->get('description')) : ''
        , 'type' => 'modContext'
        );
    }


    /** {@inheritDoc} */
    public function prepareResourceNode(modResource $resource)
    {

        $nodeName = $this->modx->getOption('resource_tree_node_name', null, 'pagetitle');
        $qtipField = $this->getProperty('qtipField');
        $nodeField = $this->getProperty('nodeField', $nodeName);


        $hasChildren = (int)$resource->get('childrenCount') > 0 && $resource->get('hide_children_in_tree') == 0 ? true : false;

        // Assign an icon class based on the class_key
        $class = $iconCls = array();
        $classKey = strtolower($resource->get('class_key'));
        if (substr($classKey, 0, 3) == 'mod') {
            $classKey = substr($classKey, 3);
        }
        $classKeyIcon = $this->modx->getOption('mgr_tree_icon_' . $classKey, null, 'tree-resource');
        $iconCls[] = $classKeyIcon;

        $class[] = 'icon-' . strtolower(str_replace('mod', '', $resource->get('class_key')));
        if (!$resource->isfolder) {
            $class[] = 'x-tree-node-leaf icon-resource';
        }
        if (!$resource->get('published')) $class[] = 'unpublished';
        if ($resource->get('deleted')) $class[] = 'deleted';
        if ($resource->get('hidemenu')) $class[] = 'hidemenu';
        if ($hasChildren) {
            $class[] = 'haschildren';
            $iconCls[] = $this->modx->getOption('mgr_tree_icon_folder', null, 'tree-folder');
            $iconCls[] = 'parent-resource';
        }

        $qtip = '';
        if (!empty($qtipField)) {
            $qtip = '<b>' . strip_tags($resource->$qtipField) . '</b>';
        } else {
            if ($resource->longtitle != '') {
                $qtip = '<b>' . strip_tags($resource->longtitle) . '</b><br />';
            }
            if ($resource->description != '') {
                $qtip = '<i>' . strip_tags($resource->description) . '</i>';
            }
        }

        $text = $resource->get($nodeField);
        //$idNote = $this->modx->hasPermission('tree_show_resource_ids') ? ' <span dir="ltr">('.$resource->id.')</span>' : '';
        $idNote = $this->modx->hasPermission('tree_show_resource_ids') ? ' (' . $resource->id . ')' : '';
        $charset = $this->modx->getOption('modx_charset', null, 'UTF-8');
        $text = htmlentities($text, ENT_QUOTES, $charset);

        $itemArray = array(
            'text' => $text . $idNote,
            'id' => $resource->context_key . '_' . $resource->id,
            'pk' => $resource->id,
            'cls' => implode(' ', $class),
            'iconCls' => implode(' ', $iconCls),
            'type' => 'modResource',
            'classKey' => $resource->class_key,
            'ctx' => $resource->context_key,
            'hide_children_in_tree' => $resource->hide_children_in_tree,
            'qtip' => $qtip,
            'checked' => in_array($resource->id, $this->resources) ? true : false,
            'disabled' => false
        );
        if (!$hasChildren) {
            $itemArray['hasChildren'] = false;
            $itemArray['children'] = array();
            $itemArray['expanded'] = true;
        } else {
            $itemArray['hasChildren'] = true;
        }

        if ($this->itemClass == 'msCategory' && $itemArray['classKey'] != 'msCategory') {
            unset($itemArray['checked']);
        }

        return $itemArray;
    }

}

return 'msImportExportResourceGetNodesProcessor';