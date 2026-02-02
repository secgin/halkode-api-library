<?php

namespace S\Halkode\Core\Abstracts\Http;

interface HttpRequest
{
    public function getUrl(): string;

    public function getHeaders(): array;

    public function getData(): array;

    public function getQueryParams(): array;

    public function getMethod(): string;
}