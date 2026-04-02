<?php
require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/trash/purge.class.php';

/**
 * Empties the recycle bin.
 *
 * @return boolean
 * @package    modx
 * @subpackage processors.resource
 */
class msImportExportResourceTrashPurgeProcessor extends modResourceTrashPurgeProcessor {

    /** @var modResource[] $resources */
    public $resources;
    /** @var array $ids The ids of the resources to be deleted. */
    public $ids;
    /** @var array $failures Failed ids of purged resources */
    private $failures;

    public function checkPermissions() {
        return true;
    }

    /**
     * @return bool|null|string
     */
    public function initialize() {
        $idlist = $this->getProperty('ids', false);

        if (!$idlist) {
            return $this->modx->lexicon('resource_err_ns');
        }

        $this->ids = explode(',', $idlist);
        $this->resources = $this->modx->getIterator('modResource', array(
            'deleted' => true,
            'id:IN' => $this->ids,
        ));

        /* validate resource can be deleted: this is necessary in advance, because
           otherwise the tvs might already have been removed, when the policy on the
           resource is checked. (just a guess, does not harm to check here and again on
           processing.
         */

        $this->failures = array();
        $success = array();
        $policies_needed = array(
            'save' => true,
            'delete' => true,
            'load' => true,
            'list' => true,
            'edit' => true,
        );
        foreach ($this->resources as $resource) {
            $context_allowed = $this->modx->getContext($resource->get('context_key'));
            $policy_allowed = $resource->checkPolicy($policies_needed);

            // again, if we do not want to allow deleting of resources in contexts we are not allowed to see, we have to check that manually
            // this _should_ be done by the resources checkPolicy
            if (!$context_allowed) {
                $this->modx->log(modX::LOG_LEVEL_WARN,
                    '[purge] context access denied for resource ' . $resource->id . ' in context ' . $resource->get('context_key'));
                $this->failures[] = $resource->id;
            }
            if (!$policy_allowed) {
                $this->modx->log(modX::LOG_LEVEL_WARN,
                    '[purge] permissions denied for resource ' . $resource->id . ': save=' . !$resource->checkPolicy(array('save')) . ', delete=' . $resource->checkPolicy(array('delete')));
                $this->failures[] = $resource->id;
            }
            if ($policy_allowed && $context_allowed) {
                $success[] = $resource->id;
            }
        }

        // we refresh the resources list here for the processor
        $this->ids = $success;
        if (empty($success)) {
            $this->resources = array();
        } else {
            $this->resources = $this->modx->getCollection('modResource', array(
                'deleted' => true,
                'id:IN' => $success,
            ));
        }

        return true;
    }

}

return 'msImportExportResourceTrashPurgeProcessor';
