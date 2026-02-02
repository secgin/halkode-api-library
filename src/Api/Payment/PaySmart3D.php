<?php

namespace S\Halkode\Api\Payment;

use S\Halkode\Core\Abstracts\Request\AbstractRequest;

class PaySmart3D extends AbstractRequest
{
    public static function create(): self
    {
        return new self([
            'cc_holder_name' => '',
            'cc_no' => '',
            'expiry_month' => '',
            'expiry_year' => '',
            'cvv' => '',

            'currency_code' => '',
            'installments_number' => 0,

            'invoice_id' => '',

            'name' => '',
            'surname' => '',

            'total' => '',
            'items' => [],

            'cancel_url' => '',
            'return_url' => ''
        ]);
    }


    /**
     * @param string $holderName      Kart sahibinin adı
     * @param string $number          Kart numarsı
     * @param string $expirationMonth Kart son kullanım ayı
     * @param string $expirationYear  Kart son kullanım yılı, dört haneli olmalı
     * @param string $cvv             Kart son kullanım yılı, dört haneli olmalı
     * @param string $currencyCode    Para birimi kodu, alabileceği değerler USD, TRY, EUR
     *
     * @return $this
     */
    public function setCreditCard(string $holderName, string $number, string $expirationMonth, string $expirationYear,
                                  string $cvv, string $currencyCode = 'TRY'): self
    {
        $this->addParams([
            'cc_holder_name' => $holderName,
            'cc_no' => $number,
            'expiry_month' => $expirationMonth,
            'expiry_year' => $expirationYear,
            'cvv' => $cvv,
            'currency_code' => $currencyCode
        ]);
        return $this;
    }

    /**
     * @param string $invoiceId          Ödeme yapılacak sepetin sipariş numarası, benzersiz göndermeye dikkat edin
     *                                   Örneğin: 4578 nolu sipariş ödemesi
     *
     * @return $this
     */
    public function setInvoiceId(string $invoiceId): self
    {
        $this->setParam('invoice_id', $invoiceId);
        return $this;
    }

    /**
     * @param string $invoiceDescription Ödeme yapılacak sepete özel bir açıklama giriniz.
     *                                   Örneğin: 4578 nolu sipariş ödemesi
     *
     * @return $this
     */
    public function setInvoiceDescription(string $invoiceDescription): self
    {
        $this->setParam('invoice_description', $invoiceDescription);
        return $this;
    }

    /**
     * @param string $name     Ürün adı
     * @param float  $price    Fiyatı
     * @param int    $quantity Miktarı
     *
     * @return $this
     */
    public function addBasketItem(string $name, float $price, int $quantity, string $description): self
    {
        $items = $this->getParam('items') ?? [];
        $items[] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'description' => $description
        ];
        $this->setParam('items', $items);

        $total = 0;
        foreach ($items as $item)
            $total += $item['price'];

        $this->setParam('total', $total);

        return $this;
    }

    /**
     * @param int $installment Taksit sayısı
     *
     * @return $this
     */
    public function setInstallment(int $installment): self
    {
        $this->setParam('installments_number', $installment);
        return $this;
    }

    public function setPerson(string $name, string $surname): self
    {
        $this->addParams([
            'name' => $name,
            'surname' => $surname
        ]);
        return $this;
    }

    /**
     * @param string      $email
     * @param string      $phone
     * @param string|null $country Adres ülke
     * @param string|null $city    Adres şehir
     * @param string|null $state   Adres ilçe
     * @param string|null $postcode
     * @param string|null $address1
     * @param string|null $address2
     *
     * @return $this
     */
    public function setBill(string  $email, string $phone, ?string $country = null, ?string $city = null,
                            ?string $state = null, ?string $postcode = null, ?string $address1 = null,
                            ?string $address2 = null): self
    {
        $billParams = [
            'bill_email' => $email,
            'bill_phone' => $phone,
            'bill_country' => $country,
            'bill_city' => $city,
            'bill_state' => $state,
            'bill_postcode' => $postcode,
            'bill_address1' => $address1,
            'bill_address2' => $address2
        ];

        foreach ($billParams as $key => $value)
        {
            if ($value != '')
                $this->setParam($key, $value);
        }
        return $this;
    }

    /**
     * @param string $type Auth: işlem tutarı karttan anında düşülür.
     *                     PreAuthorization: işlem tutarı karttan daha sonra düşülecektir.
     * @return $this
     */
    public function setTransactionType(string $type): self
    {
        $this->setParam('transaction_type', $type);
        return $this;
    }

    /**
     * @param string $value App: işlem tutarıanında gerçekleşecektir (varsayılan değerdir).
     *                      Merchant: işlem tutarı  üye iş yeri onayından sonra gerçekleşecektir.
     * @return $this
     */
    public function setPaymentCompletedBy(string $value): self
    {
        $this->setParam('payment_completed_by', $value);
        return $this;
    }

    public function setIp(string $ip): self
    {
        $this->setParam('ip', $ip);
        return $this;
    }

    public function setReturnUrl(string $returnUrl): self
    {
        $this->setParam('return_url', $returnUrl);
        return $this;
    }

    public function setCancelUrl(string $cancelUrl): self
    {
        $this->setParam('cancel_url', $cancelUrl);
        return $this;
    }

    public function getParams(): array
    {
        $params = parent::getParams();
        $params['items'] = json_encode($params['items'] ?? []);
        return $params;
    }
}