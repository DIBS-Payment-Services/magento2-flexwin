<?php
namespace Dibs\Flexwin\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Dibs\Flexwin\Model\Method;

class Void extends Request implements BuilderInterface
{
    public function build(array $buildSubject) 
    {
        $this->preRequestValidate($buildSubject);
        $find    = ['login', 'password'];
        $replace = [$this->config->getValue('api_user'), 
                    $this->config->getValue('api_password')];
        $url = str_replace($find, $replace, 
                 Method::CANCEL_URL_PATTERN);
        $merchantId = $this->config->getValue(Method::KEY_MERCHANT_NAME);
        $orderId    = $this->payment->getOrder()->getIncrementId();
        $key1 =  $this->config->getValue('md5key1');
        $key2 =  $this->config->getValue('md5key2');
        $transact = $this->payment->getLastTransId();
        return [
            'url'  => $url,
            'body' =>
            ['merchant' => $merchantId,
             'textreply'=> 'yes',
             'md5key'   => md5($key2 . md5($key1 .
                "merchant={$merchantId}&"
                . "orderid={$orderId}&"
                . "transact={$transact}")),
             'transact' => $transact,
             'orderid'  => $orderId]];
    }
}
