<?php

namespace S\Halkode\Core\Http;

use S\Halkode\Core\Abstracts\Http\HttpResult as HttpResultInterface;

final class HttpResult implements HttpResultInterface
{
    private bool $success;

    private ?string $errorCode;

    private ?string $errorMessage;

    private int $httpCode;

    private ?string $data;

    private function __construct()
    {
        $this->data = null;
        $this->errorCode = null;
        $this->errorMessage = null;
        $this->httpCode = 0;
    }

    public static function success(int $httpCode, ?string $rawResult = null): HttpResult
    {
        $result = new self();
        $result->success = true;
        $result->httpCode = $httpCode;
        $result->data = $rawResult;
        return $result;
    }

    public static function fail(int $httpCode, string $errorCode, string $errorMessage,
                                ?string $rawResult = null): HttpResult
    {
        $result = new self();
        $result->success = false;
        $result->errorCode = $errorCode;
        $result->errorMessage = $errorMessage;
        $result->httpCode = $httpCode;
        $result->data = $rawResult;
        return $result;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getContent(): ?string
    {
        return $this->data;
    }
}