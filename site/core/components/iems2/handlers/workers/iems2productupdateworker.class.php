<?php

class IeMs2ProductUpdateWorker extends MsIeResourceUpdateWorker
{
    /** @var string $classKey */
    protected $classKey = 'msProduct';

    /**
     * @param array $data
     * @param modResource $resource
     * @return array
     */
    public function makePoolQuery(array $data = array(), modResource $resource)
    {
        $pool = array();
        if ($productData = $this->findProductFields($data, true)) {
            $q = $this->buildQuery($productData, 'msProductData');
            $q->where(array(
                'id' => $resource->get('id')
            ));
            $pool[] = $q;
        }
        if ($data) {
            $pool = array_merge(
                $pool,
                parent::makePoolQuery($data, $resource)
            );
        }
        return $pool;
    }

    /**
     * @param array $fields
     * @param bool $remove
     * @return array
     */
    public function findProductFields(&$fields, $remove = true)
    {
        $result = array();
        $keys = $this->getFieldMeta('msProductData');
        foreach ($fields as $key => $val) {
            if (isset($keys[$key])) {
                $result[$key] = $val;
                if ($remove) unset($fields[$key]);
            }
        }
        return $result;
    }

}