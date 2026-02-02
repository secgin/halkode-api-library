<?php

namespace S\Halkode\Api\Payment\Handlers;

use S\Halkode\Core\Abstracts\Request\AbstractRequestHandler;
use S\Halkode\Core\Abstracts\Request\Request;
use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;
use S\Halkode\Result;

class ValidatePaymentHashHandler extends AbstractRequestHandler
{

    public function handle(Request $request): ResultInterface
    {
        $params = $request->getParams();

        $paramStatus = $params['payment_status'] ?? 0;
        $paramAmount = $params['amount'] ?? 0;
        $paramInvoiceId = $params['invoice_id'] ?? '';
        $paramOrderId = $params['order_id'] ?? '';

        list($status, $total, $invoiceId, $orderId, $currencyCode) = $this->validateHashKey(
            $params['hash_key'],
            $this->config->get('appSecret'));

        if (
            $status != $paramStatus or
            $invoiceId != $paramInvoiceId or
            $orderId != $paramOrderId
        )
        {
            return Result::fail('INVALID_HASH_KEY', 'Invalid hash key', (object)$params);
        }

        if ($paramStatus == 0)
        {
            $errorCode = $params['error_code'] ?? '';
            $errorMessage = $params['error'] ?? '';
            return Result::fail($errorCode, $errorMessage, (object)$params);
        }

        return Result::success((object)$params);
    }

    private function validateHashKey($hashKey, $secretKey): array
    {
        $status = $currencyCode = "";
        $total = $invoiceId = $orderId = 0;

        if (!empty($hashKey))
        {
            $hashKey = str_replace('__', '/', $hashKey);
            $password = sha1($secretKey);

            $components = explode(':', $hashKey);
            if (count($components) > 2)
            {
                $iv = $components[0] ?? '';
                $salt = $components[1] ?? '';
                $salt = hash('sha256', $password . $salt);
                $encryptedMsg = $components[2] ?? '';

                $decryptedMsg = openssl_decrypt($encryptedMsg, 'aes-256-cbc', $salt, 0, $iv);

                if (strpos($decryptedMsg, '|') !== false)
                {
                    $array = explode('|', $decryptedMsg);
                    $status = $array[0] ?? 0;
                    $total = $array[1] ?? 0;
                    $invoiceId = $array[2] ?? '0';
                    $orderId = $array[3] ?? 0;
                    $currencyCode = $array[4] ?? '';
                }
            }
        }

        return [$status, $total, $invoiceId, $orderId, $currencyCode];
    }
}