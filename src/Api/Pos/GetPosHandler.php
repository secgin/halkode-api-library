<?php

namespace S\Halkode\Api\Pos;

use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Request\Request;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Core\Http\HttpRequest;
use S\Halkode\Result;

class GetPosHandler extends AbstractRequestHandler
{
    public function handle(Request $request): ResultInterface
    {
        $params = $request->getParams();
        $params['merchant_key'] = $this->config->get('merchantKey');

        $httpRequest = HttpRequest::get($this->config->get('serviceUrl') . '/api/getpos')
            ->setBearerAuthentication($this->tokenStorageService->get('token')->getToken())
            ->setData($params);

        $httpResult = $this->httpClient->send($httpRequest);

        return Result::create($httpResult);
    }
}