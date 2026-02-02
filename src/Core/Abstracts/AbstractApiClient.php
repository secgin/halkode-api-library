<?php

namespace S\Halkode\Core\Abstracts;

use Exception;
use S\Halkode\Core\Abstracts\Config\Config;
use S\Halkode\Core\Abstracts\Http\HttpClient;
use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Result\Result;
use S\Halkode\Core\Abstracts\Services\AccessTokenStorage;
use S\Halkode\Core\Http\CurlHttpClient;
use S\Halkode\Core\Services\SessionAccessTokenStorage;

/**
 * @property-read HttpClient $httpClient
 */
abstract class AbstractApiClient
{
    protected Config $config;

    private HttpClient $httpClient;

    protected ?AccessTokenStorage $tokenStorage;

    private array $requestHandlerClasses;

    private array $decoratorHandlerClasses;

    public function __construct(Config $config, HttpClient $httpClient = null)
    {
        $this->config = $config;
        $this->httpClient = $httpClient ?? new CurlHttpClient();
        $this->tokenStorage = new SessionAccessTokenStorage();
        $this->requestHandlerClasses = $this->getRequestHandlerClasses();
        $this->decoratorHandlerClasses = [];
    }

    public function setTokenStorage(AccessTokenStorage $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setDecoratorHandlerClasses(array $decoratorHandlerClasses): void
    {
        $this->decoratorHandlerClasses = $decoratorHandlerClasses;
    }

    protected abstract function getRequestHandlerClasses(): array;

    private function hasRequestHandlerClass(string $name): bool
    {
        return isset($this->requestHandlerClasses[$name]);
    }

    /**
     * @param $name
     *
     * @return mixed|AbstractRequestHandler
     */
    protected function getRequestHandler($name)
    {
        $requestHandlerClass = $this->requestHandlerClasses[$name];
        $handler = new $requestHandlerClass();
        if ($handler instanceof AbstractRequestHandler)
        {
            $handler->setConfig($this->config);
            $handler->setHttpClient($this->httpClient);
            $handler->setTokenStorageService($this->tokenStorage);
        }
        return $handler;
    }

    protected function handle(string $requestName, $request): Result
    {
        $handler = $this->getRequestHandler($requestName);

        if (array_key_exists($requestName, $this->decoratorHandlerClasses))
            $handler = new $this->decoratorHandlerClasses[$requestName]($handler);

        return $handler->handle($request);
    }

    #region Magic Methods

    /**
     * @throws Exception
     */
    public function __get($name)
    {
        if ($name == 'httpClient')
            return $this->httpClient;

        throw new Exception('Undefined property via __get() (' . $name . ')');
    }

    /**
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if ($this->hasRequestHandlerClass($name))
            return $this->handle($name, $arguments[0] ?? null);

        throw new Exception('Method not found (' . $name . ')');
    }
    #endregion
}