<?php

namespace Dibs\Flexwin\Observer;
use Magento\Framework\Event\ObserverInterface;

class creditmemoRefund implements ObserverInterface {
     public function execute(\Magento\Framework\Event\Observer $observer) {
        $creditmemo = $observer['creditmemo'];
        if($creditmemo->getOrder()->getDibsFee() && $this->addDibsFeeTotal($creditmemo)) {
            $creditmemo->setDibsFee($creditmemo->getOrder()->getDibsFee());
            $creditmemo->save();
        }
    }
    
     protected function addDibsFeeTotal(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
        $creditmemoCollection = $creditmemo->getOrder()->getCreditmemosCollection();
        if($creditmemoCollection->getSize() > 0 && $creditmemo->getOrder()->getDibsFee() > 0) {
            return false;
        } else if ($creditmemo->getOrder()->getDibsFee()) {
            return true;
        } 
    }
}
