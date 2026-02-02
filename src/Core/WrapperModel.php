<?php

namespace S\Halkode\Core;

use ArrayAccess;
use Iterator;
use JsonSerializable;

final class WrapperModel implements ArrayAccess, Iterator, JsonSerializable
{
    private $data;

    public function __construct($data)
    {
        if ($data instanceof WrapperModel)
            $this->data = $data->data;
        else
            $this->data = is_object($data) ? (array) $data : $data;
    }

    private function camelToSnakeCase($camelCase): string
    {
        $result = '';

        for ($i = 0; $i < strlen($camelCase); $i++) {
            $char = $camelCase[$i];

            if (ctype_upper($char)) {
                $result .= '_' . strtolower($char);
            } else {
                $result .= $char;
            }
        }

        return ltrim($result, '-');
    }

    public function __get($name)
    {
        $name = $this->camelToSnakeCase($name);

        $value = $this->data[$name] ?? ($this->data->$name ?? null);

        if (is_object($value) || is_array($value)) {
            return new WrapperModel($value);
        }

        return $value;
    }

    #region ArrayAccess
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        $value = $this->data[$offset] ?? null;

        if (is_object($value) || is_array($value)) {
            return new WrapperModel($value);
        }

        return $value;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
    #endregion

    #region Iterator
    private int $position = 0;

    public function current()
    {
        $keys = array_keys($this->data);
        $key = $keys[$this->position] ?? null;

        if ($key === null) {
            return null;
        }

        $value = $this->data[$key];

        if (is_object($value) || is_array($value)) {
            return new WrapperModel($value);
        }

        return $value;
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        $keys = array_keys($this->data);
        return $keys[$this->position] ?? null;
    }

    public function valid()
    {
        $keys = array_keys($this->data);
        return isset($keys[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
    #endregion

    #region JsonSerializable
    public function jsonSerialize()
    {
        return $this->data;
    }
    #endregion
}