<?php

namespace Dibs\Flexwin\Block;

class Info extends \Magento\Payment\Block\Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        if ($this->getInfo()->getLastTransId()) {
            $data[(string)__('Transaction Id')] = $this->getInfo()->getLastTransId();
            if($ccLast4 = $this->getInfo()->getCcLast4()) {
               $data[(string)__('Credit card')] = '**' . $ccLast4;
            }
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }
}
