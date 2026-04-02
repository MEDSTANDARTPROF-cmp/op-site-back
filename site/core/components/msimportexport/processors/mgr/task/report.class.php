<?php

class msImportExportTaskReportProcessor extends modObjectGetProcessor
{
    public $classKey = 'MsieTask';
    public $languageTopics = array('msimportexport:default');
    /** @var Msie $msie */
    public $msie;

    public function initialize()
    {
        // $this->msie = $this->modx->getService('msimportexport', 'Msie');
        return parent::initialize();
    }

    public function cleanup()
    {
        $offset = $this->getProperty('offset', 0);
        $last = $this->getProperty('last', 0);
        $report = $this->object->getReport($last);
        $report = empty($offset) ? $report : array_slice($report, $offset);

        return $this->success('', array(
                'pid' => $this->object->get('pid'),
                'mode' => $this->object->getMode(),
                'token' => $this->object->get('token'),
                'restarted' => $this->object->get('restarted'),
                'status' => $this->object->get('status'),
                'start_time' => $this->object->get('start_time'),
                'finish_time' => $this->object->get('finish_time'),
                'report' => $report,
            )
        );
    }
}

return 'msImportExportTaskReportProcessor';