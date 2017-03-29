<?php

namespace Dibs\Flexwin\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Dibs\Flexwin\Model\Method;

class Capture extends Request implements BuilderInterface
{
    public function build(array $buildSubject) 
    {
        $this->preRequestValidate($buildSubject);
        return [
            'url'  => Method::CAPTURE_URL,
            'body' =>
            ['merchant' => $this->config->getValue('merchant'),
             'amount'   => Method::api_dibs_round($this->payment->getAmountOrdered()),
             'transact' => $this->payment->getLastTransId(),
             'orderid'  => $this->payment->getOrder()->getIncrementId()]];
    }

}
