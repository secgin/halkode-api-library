<?php

namespace S\Halkode\Core\Abstracts\Http;

interface HttpClient
{
    public function send(HttpRequest $httpRequest): HttpResult;
}