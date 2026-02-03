<?php

namespace S\Halkode;

use S\Halkode\Core\Abstracts\Http\HttpResult;
use S\Halkode\Core\Abstracts\Result\AbstractResult;
use S\Halkode\Core\WrapperModel;

/**
 * @property-read int $statusCode
 * @property-read string $statusDescription
 */
class Result extends AbstractResult
{
    private bool $success;
    private int $statusCode;
    private string $statusDescription;

    private string $errorCode;
    private string $errorMessage;

    public static function create(HttpResult $httpResult): Result
    {
        if (!$httpResult->isSuccess())
            return self::fail($httpResult->getHttpCode(), $httpResult->getErrorMessage());

        $data = json_decode($httpResult->getContent());
        if (json_last_error() !== JSON_ERROR_NONE)
            return self::fail('INVALID_DATA', 'Gelen değerler json formatında değil');

        $statusCode = $data->status_code ?? 0;
        $statusDescription = $data->status_description ?? '';

        if ($statusCode === 100)
        {
            $instance = new self();
            $instance->success = true;
            $instance->data = $data->data;
            $instance->setStatus($statusCode, $statusDescription);
            return $instance;
        }

        return self::fail($statusCode, $statusDescription, $data);
    }

    public static function success($data): Result
    {
        $result = new self();
        $result->success = true;
        $result->data = $data;
        return $result;
    }

    public static function fail(?string $errorCode, ?string $errorMessage, $data = null): Result
    {
        $result = new self();
        $result->success = false;
        $result->errorCode = $errorCode ?? '';
        $result->errorMessage = $errorMessage ?? '';
        $result->data = $data;
        return $result;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getData()
    {
        return $this->data;
    }

    private function setStatus(int $statusCode, string $statusDescription): void
    {
        $this->statusCode = $statusCode;
        $this->statusDescription = $statusDescription;
    }

    private function camelToSnakeCase($camelCase): string
    {
        $result = '';

        for ($i = 0; $i < strlen($camelCase); $i++)
        {
            $char = $camelCase[$i];

            if (ctype_upper($char))
                $result .= '_' . strtolower($char);
            else
                $result .= $char;
        }

        return ltrim($result, '-');
    }

    public function __get($name)
    {
        if (!is_object($this->data) and !is_array($this->data))
            return null;

        if ($name == 'data')
        {
            if (isset($this->data->data))
            {
                if (is_array($this->data->data) or is_object($this->data->data))
                    return new WrapperModel($this->data->data);

                return $this->data->data;
            }

            return new WrapperModel($this->data->data ?? $this->data);
        }

        if ($name == 'statusCode' or $name == 'statusDescription')
            return $this->$name;

        $propertyName = $name;
        if (isset($this->data->$propertyName))
            return $this->data->$propertyName;

        $propertyName = $this->camelToSnakeCase($name);
        if (isset($this->data->$propertyName))
            return $this->data->$propertyName;

        return parent::__get($name);
    }
}