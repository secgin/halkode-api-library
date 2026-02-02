<?php

namespace S\Halkode\Api\Payment\Handlers;

use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Request\Request;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Core\Http\HttpRequest;
use S\Halkode\Result;

class CheckPaymentStatusHandler extends AbstractRequestHandler
{

    public function handle(Request $request): ResultInterface
    {
        $params = $request->getParams();
        $params = array_merge($params, [
            'merchant_key' => $this->config->get('merchantKey')
        ]);

        $httpRequest = HttpRequest::post($this->config->get('serviceUrl') . '/api/checkstatus')
            ->setBearerAuthentication($this->tokenStorageService->get('token')->getToken())
            ->setData($params);

        $httpResult = $this->httpClient->send($httpRequest);

        return Result::create($httpResult);
    }
}