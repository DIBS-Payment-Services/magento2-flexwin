<?php

namespace Dibs\Flexwin\Observer;
use Magento\Framework\Event\ObserverInterface;

class captureOrder implements ObserverInterface {
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $invoice = $observer['invoice'];
        if($invoice->getOrder()->getDibsFee()) {
            $invoice->setDibsFee($invoice->getOrder()->getDibsFee());
            $invoice->save();
        }
    }
}
