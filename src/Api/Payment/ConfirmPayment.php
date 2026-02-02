<?php

namespace S\Halkode\Api\Payment;

use S\Halkode\Core\Abstracts\Request\AbstractRequest;

class ConfirmPayment extends AbstractRequest
{
    /**
     * @param string $invoiceId
     * @param string $status 1:Onaylandı, 2:İptal
     * @param float  $total
     *
     * @return ConfirmPayment
     */
    public static function create(string $invoiceId, string $status, float $total): ConfirmPayment
    {
        return new self([
            'invoice_id' => $invoiceId,
            'status' => $status,
            'total' => $total
        ]);
    }
}