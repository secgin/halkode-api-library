<?php

namespace S\Halkode\Api;

use S\Halkode\Api\Authorization\GetAccessToken;
use S\Halkode\Api\Authorization\GetAccessTokenHandler;
use S\Halkode\Api\Payment\Handlers\PaySmart3DHandler;
use S\Halkode\Api\Payment\Handlers\ValidatePaymentHashHandler;
use S\Halkode\Api\Pos\GetPosHandler;
use S\Halkode\Completion\GetAccessTokenResult;
use S\Halkode\Core\Abstracts\AbstractApiClient;
use S\Halkode\Core\Abstracts\Result\Result;
use S\Halkode\Core\Services\SimpleAccessToken;

class HalkodeApiClient extends AbstractApiClient implements \S\Halkode\HalkodeApiClient
{

    protected function getRequestHandlerClasses(): array
    {
        return [
            'getAccessToken' => GetAccessTokenHandler::class,
            'paySmart3D' => PaySmart3DHandler::class,
            'getPos' => GetPosHandler::class,
            'validatePaymentHash' => ValidatePaymentHashHandler::class,
        ];
    }

    protected function handle(string $requestName, $request): Result
    {
        if (
            $requestName != 'getAccessToken' and
            (
                !$this->tokenStorage->has('token') or
                $this->tokenStorage->get('token')->isExpirationPassed('Europe/Istanbul')
            )
        )
        {
            $this->refreshToken();
        }

        $result = parent::handle($requestName, $request);

        if (!$result->isSuccess() and $result->getErrorCode() == '401')
        {
            $this->refreshToken();
            return parent::handle($requestName, $request);
        }

        return $result;
    }

    private function refreshToken(): void
    {
        $handler = $this->getRequestHandler('getAccessToken');

        /** @var Result|GetAccessTokenResult $result */
        $result = $handler->handle(GetAccessToken::create());
        if ($result->isSuccess())
        {
            $accessToken = new SimpleAccessToken($result->token, date_create($result->expiresAt));
            $this->tokenStorage->set('token', $accessToken);
        }
    }
}