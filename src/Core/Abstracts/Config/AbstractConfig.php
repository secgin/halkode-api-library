<?php

namespace S\Halkode\Core\Abstracts\Config;

use Exception;

/**
 * @method static static create(array $configs = [])
 */
abstract class AbstractConfig implements Config
{
    private array $items = [];

    public function __construct(array $config)
    {
        $this->items = $config;
    }

    public function get(string $key): string
    {
        return $this->items[$key] ?? '';
    }

    public function set(string $key, $value): void
    {
        $this->items[$key] = $value;
    }

    public function __get($name)
    {
        if (isset($this->items[$name]))
            return $this->get($name);

        return null;
    }

    public function __call($name, $arguments)
    {
        if (count($arguments) === 0)
            return $this->get($name);

        $this->set($name, $arguments[0]);
        return $this;
    }

    /**
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name == 'create')
            return new static(...$arguments);

        throw new Exception("Call to undefined method " . __CLASS__ . "::" . $name . "()");
    }
}