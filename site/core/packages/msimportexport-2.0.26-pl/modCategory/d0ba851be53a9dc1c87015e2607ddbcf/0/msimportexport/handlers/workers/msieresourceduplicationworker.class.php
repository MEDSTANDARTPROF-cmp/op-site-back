<?php

class MsIeResourceDuplicationWorker extends MsIeWorker
{
    /** @var  MsieTask $sourceTask */
    public $sourceTask;
    /** @var int $total */
    protected $total = 0;
    /** @var array $duplication */
    protected $duplication = array();
    /** @var string $workingDirectory */
    protected $workingDirectory = '';

    public function initialize()
    {
        $initialized = parent::initialize();
        if ($initialized === true) {
            $taskId = $this->getSetting('source_task', 0);
            $this->sourceTask = $this->modx->getObject('MsieTask', $taskId);
            if ($this->sourceTask) {
                $this->workingDirectory = $this->sourceTask->getSetting('working_directory', '');
                $this->getDuplication();
                if (!empty($this->duplication) && !empty($this->workingDirectory)) {
                    $this->total = $this->calculationTotal();
                    return true;
                }
            }
            $initialized = false;
        }
        return $initialized;
    }

    /**
     * @return array
     */
    public function getDuplication()
    {
        if ($this->sourceTask && !$this->duplication) {
            $state = $this->sourceTask->getState();
            $storage = new MsIeArrStorage();
            $storage->unserialize($state['storage']);
            $this->duplication = $storage->getStore('duplication', array());
        }
        return $this->duplication;
    }

    /**
     * @return bool
     */
    public function process()
    {
        $settings = $this->sourceTask->getSettings();
        $this->beforeStart();
        foreach ($this->duplication as $filename => $rows) {
            $file = $this->workingDirectory . $filename;
            if (!file_exists($file)) continue;
            if (!$reader = $this->tools->getReader($file)) continue;
            if (!$this->writer) {
                $type = $this->tools->getFileExtension($file);
                $this->file = $this->workingDirectory . 'duplication_' . $filename;
                if (!$this->writer = $this->getWriter($type)) continue;
                $this->writer->initialize($settings);
            }
            $reader->initialize($settings);
            $reader->open($file);
            $reader->onEvent('read', array($this, 'iterate'));
            foreach ($rows as $offset) {
                $reader->setOffset($offset);
                $reader->read();
            }
            $reader->close();
        }
        if ($this->file) {
            if (!$this->writer->save($this->file)) {
                $this->file = '';
            }
        }
        $this->afterFinish();
        return true;
    }

    /**
     * @param array $data
     * @param MsIeReader $reader
     */
    public function iterate(array $data, MsIeReader $reader)
    {
        if ($this->iteration % $this->iterationReport == 0) {
            $this->saveReport();
        }
        $reader->stop();
        $this->writer->write($data);

        $this->iteration++;

    }

    /**
     * @return int
     */
    public function calculationTotal()
    {
        $result = 0;
        if ($this->duplication) {
            foreach ($this->duplication as $rows) {
                $result += count($rows);
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function prepareReportData()
    {
        $data = parent::prepareReportData();
        $data['total'] = $this->total;
        if ($this->file) {
            $data['file'] = $this->file;
            $data['cached'] = 0;
            $data['download'] = $this->getDownloadUrl();
        }
        return $data;
    }

}