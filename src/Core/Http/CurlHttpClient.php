<?php

namespace S\Halkode\Core\Http;

use S\Halkode\Core\Abstracts\Http\HttpClient;
use S\Halkode\Core\Abstracts\Http\HttpRequest;
use S\Halkode\Core\Abstracts\Http\HttpRequestHandleListener;

final class CurlHttpClient implements HttpClient
{
    private ?HttpRequestHandleListener $requestHandleListener = null;

    private ?string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
    }

    public function setRequestHandleListener(?HttpRequestHandleListener $requestHandleListener): void
    {
        $this->requestHandleListener = $requestHandleListener;
    }

    public function send(HttpRequest $httpRequest): HttpResult
    {
        $httpHeader = array_map(function ($key, $value)
        {
            return $key . ': ' . $value;
        }, array_keys($httpRequest->getHeaders()), $httpRequest->getHeaders());

        $postFields = $this->isContentTypeUrlencoded($httpRequest)
            ? http_build_query($httpRequest->getData())
            : json_encode($httpRequest->getData(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        $options = [
            CURLOPT_HTTPHEADER => $httpHeader,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_CUSTOMREQUEST => $httpRequest->getMethod(),
            CURLOPT_POSTFIELDS => $postFields
        ];

        $url = $this->baseUrl . $httpRequest->getUrl();
        if (!empty($httpRequest->getQueryParams()))
            $url .= '?' . http_build_query($httpRequest->getQueryParams());

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);

        if ($this->requestHandleListener != null)
            $this->requestHandleListener->beforeRequest($httpRequest);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if ($result === false)
        {
            $requestResult = HttpResult::fail($httpCode, curl_errno($ch), curl_error($ch));
        }
        else
        {
            if ($httpCode >= 200 and $httpCode<300)
                $requestResult = HttpResult::success($httpCode, $result);
            else
                $requestResult = HttpResult::fail($httpCode, curl_errno($ch), curl_error($ch), $result);
        }

        if ($this->requestHandleListener != null)
            $this->requestHandleListener->afterRequest($httpRequest, $requestResult);

        curl_close($ch);
        return $requestResult;
    }

    private function isContentTypeUrlencoded(HttpRequest $httpRequest): bool
    {
        $headers = $httpRequest->getHeaders();
        $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? null;

        return $contentType == 'application/x-www-form-urlencoded';
    }
}