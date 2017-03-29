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
        return ['url' => $url,
               'body' =>
               ['merchant' => $this->config->getValue('merchant'),
                'amount'   => Method::api_dibs_round($this->prepareAmount($buildSubject['amount'])),
                'transact' => $this->payment->getLastTransId(),
                'orderid'  => $this->payment->getOrder()->getIncrementId()]];
    }
}
