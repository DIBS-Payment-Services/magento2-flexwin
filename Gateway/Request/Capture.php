<?php

namespace Dibs\Flexwin\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Dibs\Flexwin\Model\Method;

class Capture extends Request implements BuilderInterface
{
    public function build(array $buildSubject) 
    {
        $this->preRequestValidate($buildSubject);
        $merchantId = $this->config->getValue(Method::KEY_MERCHANT_NAME);
        $amount = Method::api_dibs_round($this->subjectReader->readAmount($buildSubject));
        $orderId    = $this->payment->getOrder()->getIncrementId();
        $key1 =  $this->config->getValue('md5key1');
        $key2 =  $this->config->getValue('md5key2');
        $transact = $this->payment->getLastTransId();
        return [
            'url'  => Method::CAPTURE_URL,
            'body' =>
            [Method::KEY_MERCHANT_NAME => $merchantId,
             Method::KEY_AMOUNT_NAME   => $amount,
             'transact' => $transact,
             'md5key'  => md5($key2 . md5($key1 . "merchant={$merchantId}&"
             . "orderid={$orderId}&"
             . "transact={$transact}&"
             . "amount={$amount}")),
             Method::KEY_ORDERID_NAME  => $orderId]];
    }

}
