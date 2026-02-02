<?php

namespace S\Halkode\Api\Payment;

use S\Halkode\Core\Abstracts\Request\AbstractRequest;

class CheckPaymentStatus extends AbstractRequest
{
    public static function create(string $invoiceId): CheckPaymentStatus
    {
        return new self([
            'merchant_key' => '',
            'invoice_id' => $invoiceId
        ]);
    }
}