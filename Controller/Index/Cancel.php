<?php

namespace Dibs\Flexwin\Controller\Index;

class Cancel extends \Dibs\Flexwin\Controller\Index
{
    public function execute()
    {
        if ($this->checkPost()) {
            $orderId = $this->getRequest()->getParam(\Dibs\Flexwin\Model\Method::KEY_ORDERID_NAME);
            $this->method->setOrderCancelled($orderId);
            $this->method->restoreQuoteFromOrder($orderId);
        }
        $this->_redirect('checkout');
    }
}