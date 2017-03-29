<?php

namespace Dibs\Flexwin\Controller\Index;

class Accept extends \Dibs\Flexwin\Controller\Index
{
    public function execute()
    {
        if ($this->checkPost()) {
            $order = $this->order->loadByIncrementId($this->getRequest()->getParam(\Dibs\Flexwin\Model\Method::KEY_ORDERID_NAME));
            $this->checkoutSession->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId());
            $this->checkoutSession->setLastQuoteId($order->getQuoteId())->setLastSuccessQuoteId($order->getQuoteId());
            $this->method->completeCheckout();
        }
        $this->_redirect('checkout/onepage/success');
    }
}
