<?php

class MsIeResourceDuplicationService extends MsIeService
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'duplication';
    }

    /**
     * @return array
     */
    public function getLexiconTopics()
    {
        return array('msimportexport:service_resource');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return true;
    }

    /**
     * @param array $properties
     * @return MsIeResourceDuplicationWorker|null
     */

    public function getWorker(array $properties = array())
    {
        return $this->loadWorker('MsIeResourceDuplicationWorker', $properties);
    }

}