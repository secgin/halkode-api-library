<?php

namespace S\Halkode\Api\Authorization;

use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Request\Request;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Core\Http\HttpRequest;
use S\Halkode\Result;

class GetAccessTokenHandler extends AbstractRequestHandler
{
    public function handle(Request $request): ResultInterface
    {
        $serviceUrl = $this->config->get('serviceUrl');
        $appId = $this->config->get('appId');
        $appSecret = $this->config->get('appSecret');

        $httpRequest = HttpRequest::post($serviceUrl . '/api/token')
            ->setData([
                'app_id' => $appId,
                'app_secret' => $appSecret,
            ]);

        $httpResult = $this->httpClient->send($httpRequest);

        return Result::create($httpResult);
    }
}