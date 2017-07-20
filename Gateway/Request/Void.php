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
        return [
            'url'  => $url,
            'body' =>
            ['merchant' => $this->config->getValue('merchant'),
             'textreply'=> 'yes',
             'transact' => $this->payment->getLastTransId(),
             'orderid'  => $this->payment->getOrder()->getIncrementId()]];
    }

}