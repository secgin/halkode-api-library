<?php

namespace S\Halkode\Api\Payment\Handlers;

use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Request\Request;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Core\Http\HttpRequest;
use S\Halkode\Result;

class ConfirmPaymentHandler extends AbstractRequestHandler
{
    public function handle(Request $request): ResultInterface
    {
        $params = $request->getParams();

        $merchantKey = $this->config->get('merchantKey');
        $appSecret = $this->config->get('appSecret');
        $invoiceId = $params['invoice_id'] ?? '';
        $status = $params['status'] ?? '';
        $total = $params['total'];

        $hashKey = $this->generateConfirmPaymentHashKey($merchantKey, $invoiceId, $status, $appSecret);

        $httpRequest = HttpRequest::post($this->config->get('serviceUrl') . '/api/confirmPayment')
            ->setBearerAuthentication($this->tokenStorageService->get('token')->getToken())
            ->setData([
                'invoice_id' => $invoiceId,
                'merchant_key' => $merchantKey,
                'status' => $status,
                'hash_key' => $hashKey,
                'total' => $total
            ]);

        $httpResult = $this->httpClient->send($httpRequest);

        return Result::create($httpResult);
    }

    private function generateConfirmPaymentHashKey($merchant_key, $invoice_id, $status, $app_secret)
    {
        $data = $merchant_key . '|' . $invoice_id . '|' . $status;

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $saltWithPassword, 0, $iv);
        $msg_encrypted_bundle = $iv . ':' . $salt . ':' . $encrypted;
        return str_replace('/', '__', $msg_encrypted_bundle);
    }
}