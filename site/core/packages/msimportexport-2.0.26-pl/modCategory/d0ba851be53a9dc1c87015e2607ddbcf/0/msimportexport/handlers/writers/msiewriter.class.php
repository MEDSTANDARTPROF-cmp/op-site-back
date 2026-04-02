<?php

interface MsIeWriterInterface
{

    /**
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function write(array $data, array $options);

    /**
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function save($path, array $options = array());

    public function close();

    /**
     * @return string
     */
    public function getType();

}

abstract class MsIeWriter implements MsIeWriterInterface
{

    /** @var modX $modx */
    protected $modx;
    protected $offset = 0;
    protected $config = array();

    /**
     * MsIeWriter constructor.
     * @param $modx
     * @param array $config
     */
    public function __construct(&$modx, array $config = array())
    {
        $this->modx = &$modx;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param array $config
     * @return bool
     */
    public function initialize(array $config = array())
    {
        $this->config = $config;
        return true;
    }

    /**
     * @param $file
     * @param int $cell
     * @param int $row
     * @param array $options
     * @return bool
     */
    public function attach($file, $cell, $row, array $options = array())
    {
        return true;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getSysTmpPath()
    {
        $path = realpath(sys_get_temp_dir());
        if (ini_get('upload_tmp_dir') !== false) {
            if ($temp = ini_get('upload_tmp_dir')) {
                if (file_exists($temp)) {
                    $path = realpath($temp);
                }
            }
        }
        return $path . '/';
    }

    /**
     * @param string $path
     * @return string
     */
    public function preparePath($path)
    {
        if (substr($path, -1) == '/') {
            $path .= date('Y-m-d_h:i:s') . '.' . $this->getType();
        } else if (empty(pathinfo($path, PATHINFO_EXTENSION))) {
            $path .= '.' . $this->getType();
        }
        return $path;
    }

}

