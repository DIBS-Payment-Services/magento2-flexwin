<?php

namespace Dibs\Flexwin\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Dibs\Flexwin\Model\Method;


class Refund extends Request implements BuilderInterface 
{
    public function build(array $buildSubject) {
        $this->preRequestValidate($buildSubject);
        $find    = ['login', 'password'];
        $replace = [$this->config->getValue('api_user'), 
                    $this->config->getValue('api_password')];
        $url = str_replace($find, $replace, 
                 Method::REFUND_URL_PATTERN);
        $merchantId = $this->config->getValue(Method::KEY_MERCHANT_NAME);
        $amount     = Method::api_dibs_round($this->subjectReader->readAmount($buildSubject));
        $orderId    = $this->payment->getOrder()->getIncrementId();
        $key1 =  $this->config->getValue('md5key1');
        $key2 =  $this->config->getValue('md5key2');
        preg_match('/[0-9]*/', $this->payment->getLastTransId(), $matches, PREG_OFFSET_CAPTURE);
        $transact = $matches[0][0];
        return ['url' => $url,
               'body' =>
               ['merchant' => $merchantId,
                'amount'   => $amount,
                'transact' => $transact,
                'orderid'  => $orderId,
                'md5key'  => md5($key2 . md5($key1 . "merchant={$merchantId}&"
                . "orderid={$orderId}&"
                . "transact={$transact}&"
                . "amount={$amount}"))]];
    }
}
