<?php

namespace S\Halkode\Core\Abstracts\Http;

interface HttpResult
{
    public function isSuccess(): bool;

    public function getErrorCode(): ?string;

    public function getErrorMessage(): ?string;

    public function getHttpCode(): int;

    public function getContent(): ?string;
}