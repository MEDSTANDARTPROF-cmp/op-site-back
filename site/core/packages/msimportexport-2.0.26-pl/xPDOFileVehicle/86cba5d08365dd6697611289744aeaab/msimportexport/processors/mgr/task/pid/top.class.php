<?php

class msImportExportTaskGetPidProcessor extends modObjectGetProcessor
{
    public $classKey = 'MsieTask';
    public $languageTopics = array('msimportexport:default');

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        exec(' top -b -n 1 -p ' . $this->object->get('pid'), $top);
        if (empty($top)) {
            $top = array();
        } else {
            $top = array_filter($top);
            $top = array_keys(array_flip($top));
            if (count($top) == 7) {
                $tmp = array_filter(explode(' ', array_pop($top)));
                $tmp = array_map('trim', $tmp);
                array_pop($tmp);
                $top[] = implode(' ', $tmp);
            } else {
                array_pop($top);
            }
        }
        return $this->success('', array(
            'status' => $this->object->get('status'),
            'top' => $top,
        ));
    }

}

return 'msImportExportTaskGetPidProcessor';