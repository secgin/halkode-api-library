<?php

namespace S\Halkode\Core\Abstracts\Request;

use Exception;

/**
 * @method static static create(array $params = [])
 */
abstract class AbstractRequest implements Request
{
    private array $params;

    protected function __construct(array $params = [])
    {
        $this->params = $params;
    }

    protected function setParams(array $params): void
    {
        $this->params = $params;
    }

    protected function setParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    protected function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParam(string $key)
    {
        return $this->params[$key];
    }

    public function __call($name, $arguments)
    {
        if (isset($arguments[0]))
            $this->params[$name] = $arguments[0];

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