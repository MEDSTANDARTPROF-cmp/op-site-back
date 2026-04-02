<?php

use \JsonStreamingParser\Listener\InMemoryListener;
use JsonStreamingParser\Listener\IdleListener;
use \JsonStreamingParser\Parser;

class MsIeJSONReader extends MsIeReader
{

    /** @var Parser $reader */
    protected $reader;
    /** @var IdleListener $listener */
    protected $listener;
    /** @var string $lineEnding */
    protected $lineEnding = "\n";
    /** @var bool $emitWhitespace */
    protected $emitWhitespace = false;
    /** @var int $bufferSize */
    protected $bufferSize = 8192;

    /**
     * @param array $config
     */
    public function initialize(array $config = array())
    {
        parent::initialize($config);
        $this->listener = new InMemoryListener();
    }

    /**
     * @param string $file
     * @return bool
     */
    public function open($file)
    {
        if (!parent::open($file)) return false;
        try {
            /** @var resource $stream */
            $stream = fopen($file, 'r');
            $this->reader = new Parser(
                $stream,
                $this->getListener(),
                $this->getLineEnding(),
                $this->isEmitWhitespace(),
                $this->getBufferSize()
            );
            $this->reader->parse();
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            fclose($stream);
            return false;
        }
        fclose($stream);
        return true;
    }


    /**
     * @param callable $callback
     * @return bool
     */
    public function read(callable $callback = null)
    {
        $result = false;
        try {
            if ($json = $this->listener->getJson()) {
                $total = count($json);
                for ($i = $this->offset; $i < $total; $i++) {
                    $this->setOffset($i);
                    if (!$this->proceed) break;
                    $this->record = $json[$i];
                    if (is_callable($callback)) {
                        if ($callback($this, $this->record) !== true) {
                            $this->close();
                            return true;
                        }
                    } else {
                        $this->fireEvent('read', array('data' => $this->record));
                    }
                }
            }
            $result = true;
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
        }

        return $result;
    }

    /**
     * @return IdleListener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param IdleListener $listener
     * @return $this
     */
    public function setListener(IdleListener $listener)
    {
        $this->listener = $listener;
        return $this;
    }

    /**
     * @return string
     */
    public function getLineEnding()
    {
        return $this->lineEnding;
    }

    /**
     * @param string $lineEnding
     */
    public function setLineEnding($lineEnding)
    {
        $this->lineEnding = $lineEnding;
    }

    /**
     * @return bool
     */
    public function isEmitWhitespace()
    {
        return $this->emitWhitespace;
    }

    /**
     * @param bool $emitWhitespace
     */
    public function setEmitWhitespace($emitWhitespace)
    {
        $this->emitWhitespace = $emitWhitespace;
    }

    /**
     * @return int
     */
    public function getBufferSize()
    {
        return $this->bufferSize;
    }

    /**
     * @param int $bufferSize
     */
    public function setBufferSize($bufferSize)
    {
        $this->bufferSize = $bufferSize;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_JSON;
    }

}