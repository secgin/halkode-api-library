<?php

namespace S\Halkode\Api\Payment\Handlers;

use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Request\Request;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Core\Http\HttpRequest;
use S\Halkode\Result;

class PaySmart3DHandler extends AbstractRequestHandler
{
    public function handle(Request $request): ResultInterface
    {
        $params = $request->getParams();
        $params = array_merge([
            'merchant_key' => $this->config->get('merchantKey'),
            'hash_key' => $this->prepareHashKey($params),
        ], $params);

        $httpRequest = HttpRequest::post($this->config->get('serviceUrl') . '/api/paySmart3D')
            ->setBearerAuthentication($this->tokenStorageService->get('token')->getToken())
            ->setData($params);

        $httpResult = $this->httpClient->send($httpRequest);
        if ($httpResult->isSuccess())
            return Result::success($httpResult->getContent());

        return Result::fail($httpResult->getErrorCode(), $httpResult->getErrorMessage(), $httpResult->getContent());
    }

    private function prepareHashKey(array $params): string
    {
        $total = $params['total'];
        $installmentsNumber = $params['installments_number'];
        $currencyCode = $params['currency_code'];
        $invoiceId = $params['invoice_id'];
        $merchantKey = $this->config->get('merchantKey');
        $appSecret = $this->config->get('appSecret');

        return $this->generateHashKey($total, $installmentsNumber, $currencyCode, $merchantKey, $invoiceId, $appSecret);
    }

    private function generateHashKey(float  $total, int $installment, string $currencyCode, string $merchantKey,
                                     string $invoiceId, string $appSecret)
    {
        $data = $total . '|' . $installment . '|' . $currencyCode . '|' . $merchantKey . '|' . $invoiceId;

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($appSecret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $saltWithPassword, 0, $iv);

        $msg_encrypted_bundle = $iv . ':' . $salt . ':' . $encrypted;
        return str_replace('/', '__', $msg_encrypted_bundle);
    }
}