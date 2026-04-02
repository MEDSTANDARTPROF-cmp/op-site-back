<?php

interface MsIeStorage
{
    /**
     * @param string $storeKey
     * @param mixed|null $default
     * @param bool $skipEmpty
     * @return mixed|null
     */
    public function getStore($storeKey, $default = null, $skipEmpty = true);

    /**
     * @param string $storeKey
     * @param mixed $v
     */
    public function setStore($storeKey, $v);

    /**
     * @param string $storeKey
     * @param mixed $v
     * @param mixed|null $key
     * @param bool $kit
     */
    public function pushStore($storeKey, $v, $key = null, $kit = false);

    /**
     * @param string $storeKey
     * @param mixed $key
     * @param null $default
     * @return mixed|null
     */
    public function getStoreVal($storeKey, $key, $default = null);

    /**
     * @param string $storeKey
     * @param mixed $v
     * @return bool|false|int|string
     */
    public function hasValInStore($storeKey, $v);

    /**
     * @param string $storeKey
     * @param mixed $key
     * @return bool
     */
    public function hasKeyInStore($storeKey, $key);

    /**
     * @return array
     */
    public function getStorage();

    /**
     * @param mixed $storage
     */
    public function setStorage($storage);

    /**
     * @return string
     */
    public function serialize();

    /**
     * @param string $str
     */
    public function unserialize($str);

}
