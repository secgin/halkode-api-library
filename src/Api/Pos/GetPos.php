<?php

namespace S\Halkode\Api\Pos;

use S\Halkode\Core\Abstracts\Request\AbstractRequest;

class GetPos extends AbstractRequest
{
    /**
     * @param string $creditCardNumber Kart numarsının ilk 6 hanesi;
     *                                 Tarım kartları için kart numarasının tamamı gereklidir
     * @param float  $amount           Ödeme miktarı
     * @param string $currencyCode     Para birimi (TRY)
     */
    public static function create(string $creditCardNumber, float $amount, string $currencyCode): GetPos
    {
        return new self([
            'credit_card' => $creditCardNumber,
            'amount' => $amount,
            'currency_code' => $currencyCode,
            'merchant_key' => ''
        ]);
    }
}