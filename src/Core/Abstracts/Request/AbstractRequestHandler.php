<?php

namespace S\Halkode\Core\Abstracts\Request;

use S\Halkode\Core\Abstracts\Config\Config;
use S\Halkode\Core\Abstracts\Http\HttpClient;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Core\Abstracts\Services\AccessTokenStorage;

abstract class AbstractRequestHandler implements RequestHandler
{
    protected Config $config;

    protected HttpClient $httpClient;

    protected AccessTokenStorage $tokenStorageService;

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function setTokenStorageService(AccessTokenStorage $tokenStorageService): void
    {
        $this->tokenStorageService = $tokenStorageService;
    }

    public abstract function handle(Request $request): ResultInterface;
}