<?php

namespace S\Halkode\Core\Abstracts\Http;

interface HttpRequestHandleListener
{
    public function beforeRequest(HttpRequest $request): void;

    public function afterRequest(HttpRequest $request, HttpResult $httpResult): void;
}