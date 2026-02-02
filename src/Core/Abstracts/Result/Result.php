<?php

namespace S\Halkode\Core\Abstracts\Result;

interface Result
{
    public function isSuccess(): bool;

    public function getErrorCode(): string;

    public function getErrorMessage(): string;

    /**
     * @return mixed
     */
    public function getData();
}