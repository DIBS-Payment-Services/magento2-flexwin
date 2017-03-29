<?php

namespace Dibs\Flexwin\Observer;

use Magento\Framework\Event\ObserverInterface;

class paymentPlaceStart implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer['payment'];

        // prevent sending email untill payment 
        if ($payment->getMethod() == \Dibs\Flexwin\Model\ConfigProvider::METHOD_CODE) {
            $order = $payment->getOrder();
            $order->setCanSendNewEmailFlag(false);
        }
    }
}
