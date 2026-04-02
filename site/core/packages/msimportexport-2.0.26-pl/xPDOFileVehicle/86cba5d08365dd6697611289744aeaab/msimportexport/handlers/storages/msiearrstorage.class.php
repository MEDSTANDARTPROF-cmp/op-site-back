<?php

class MsIeArrStorage implements MsIeStorage
{
    /** @var array */
    protected $storage = array();

    
    /**
     * @param string $storeKey
     * @param mixed|null $default
     * @param bool $skipEmpty
     * @return mixed|null
     */
    public function getStore($storeKey, $default = null, $skipEmpty = true)
    {
        $val = array_key_exists($storeKey, $this->storage) ? $this->storage[$storeKey] : $default;
        if ($val === '' && $skipEmpty) {
            $val = $default;
        }
        return $val;
    }

    /**
     * @param string $storeKey
     * @param mixed $v
     */
    public function setStore($storeKey, $v)
    {
        $this->storage[$storeKey] = $v;
    }

    /**
     * @param string $storeKey
     * @param mixed $v
     * @param mixed|null $key
     * @param bool $kit
     */
    public function pushStore($storeKey, $v, $key = null, $kit = false)
    {
        if (!isset($this->storage[$storeKey])) {
            $this->storage[$storeKey] = array();
        }
        if ($key === null) {
            $this->storage[$storeKey][] = $v;
        } else {
            $key = trim($key);
            if ($kit) {
                if (!isset($this->storage[$storeKey][$key])) {
                    $this->storage[$storeKey][$key] = array();
                }
                $this->storage[$storeKey][$key][] = $v;
            } else {
                $this->storage[$storeKey][$key] = $v;
            }
        }
    }

    /**
     * @param string $storeKey
     * @param mixed $key
     * @param null $default
     * @return mixed|null
     */
    public function getStoreVal($storeKey, $key, $default = null)
    {
        return $this->hasKeyInStore($storeKey, $key) ? $this->storage[$storeKey][$key] : $default;
    }

    /**
     * @param string $storeKey
     * @param mixed $v
     * @return bool|false|int|string
     */
    public function hasValInStore($storeKey, $v)
    {
        if (empty($this->storage[$storeKey])) return false;
        return array_search($v, $this->storage[$storeKey]);
    }

    /**
     * @param string $storeKey
     * @param mixed $key
     * @return bool
     */
    public function hasKeyInStore($storeKey, $key)
    {
        if (empty($this->storage[$storeKey])) return false;
        return array_key_exists(trim($key), $this->storage[$storeKey]);
    }

    /**
     * @return array
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param array $storage
     */
    public function setStorage($storage = array())
    {
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->storage);
    }

    /**
     * @param string $str
     */
    public function unserialize($str)
    {
        $this->storage = unserialize($str);
    }

}
