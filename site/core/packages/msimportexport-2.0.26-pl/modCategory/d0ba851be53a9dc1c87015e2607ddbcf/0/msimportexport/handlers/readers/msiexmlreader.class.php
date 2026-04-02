<?php

/**
 * https://ikfi.ru/article/parsim-xml-s-pomoschju-xmlreader
 */
class MsIeXMLReader extends MsIeReader
{

    /** @var XMLReader $reader */
    protected $reader;

    /**
     * @param array $config
     */
    public function initialize(array $config = array())
    {
        parent::initialize($config);
        $this->reader = new XMLReader();
    }

    /**
     * @param string $file
     * @return bool
     */
    public function open($file)
    {
        if (!parent::open($file)) return false;
        try {
            $this->reader->open($file);
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return false;
        }
        return true;
    }

    /**
     * @param callable $callback
     * @return bool
     */
    public function read(callable $callback = null)
    {
        $result = true;
        $this->reader->read();
        while ($this->proceed && $this->reader->read()) {
            if (is_callable($callback)) {
                if ($callback($this, $this->reader->nodeType) !== true) {
                    $this->close();
                    return $result;
                }
            } else {
                if ($this->reader->nodeType == XMLREADER::ELEMENT) {
                    $fnName = 'read' . $this->reader->localName;
                    if (method_exists($this, $fnName)) {
                        $lcn = $this->reader->name;
                        $this->fireEvent('beforeReadContainer', array('name' => $lcn));
                        if ($this->reader->name == $lcn && $this->reader->nodeType != XMLREADER::END_ELEMENT) {
                            $this->fireEvent('beforeReadElement', array('name' => $lcn));
                            $this->{$fnName}();
                            $this->fireEvent($fnName);
                            $this->fireEvent('afterReadElement', array('name' => $lcn));
                        } elseif ($this->reader->nodeType == XMLREADER::END_ELEMENT) {
                            $this->fireEvent('afterReadContainer', array('name' => $lcn));
                        }
                    }
                }
            }
        }
        $this->close();
        return $result;

    }

    public function close()
    {
        if($this->reader) {
            $this->reader->close();
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_XML;
    }

}