<?php

namespace S\Halkode\Core\Abstracts\Result;

use Exception;
use S\Halkode\Core\Abstracts\Http\HttpResult;

/**
 * @method static static create(HttpResult $httpResult)
 */
abstract class AbstractResult implements Result
{
    /**
     * @var mixed
     */
    protected $data;

    protected function __construct()
    {
    }

    public function __get($name)
    {
        if (isset($this->data->{$name}))
            return $this->data->{$name};

        return null;
    }

    /**
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name == 'create' and isset($arguments[0]) and $arguments[0] instanceof HttpResult)
            return static::create($arguments[0]);

        throw new Exception("Call to undefined method " . __CLASS__ . "::" . $name . "()");
    }
}