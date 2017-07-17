<?php

namespace Hsoderlind\FatfreePlugins\Http;

class DataContainer implements ArrayAccess
{
    private $_container = [];

    private $_storageKey;

    public function __construct($storageKey)
    {
        $this->_storageKey = $storageKey;
        $this->_container = [];
    }

    public function offsetExists($offset)
    {
        return isset($this->_container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_container[$offset]) ? $this->_container[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_container[] = $value;
        } else {
            $this->_container[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->_container[$offset]);
    }
}
