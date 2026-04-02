<?php

class msImportExportWatcherGetPidProcessor extends modProcessor
{

    public $languageTopics = array('msimportexport:default');

    public function process()
    {
        return $this->cleanup();
    }

    /**
     * Return the response
     * @return array
     */
    public function cleanup()
    {
        exec(' top -b -n 1 -p ' . $this->getProperty('pid', 0), $top);
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
            'top' => $top,
        ));
    }

}

return 'msImportExportWatcherGetPidProcessor';