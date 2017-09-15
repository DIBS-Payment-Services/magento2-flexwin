<?php

namespace Dibs\Flexwin\Model\Sales\Order\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Totals extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        if($this->addDibsFeeTotal($creditmemo)) { 
            $creditmemo->setFee(0);
            $creditmemo->setBaseFee(0);
            $amount = $creditmemo->getOrder()->getDibsFee();
            $creditmemo->setFee($amount);
            $amount = $creditmemo->getOrder()->getDibsFee();
            $creditmemo->setBaseFee($amount);
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getOrder()->getDibsFee() / 100);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getOrder()->getDibsFee() / 100);
        }
        return $this;
    }
    
    protected function addDibsFeeTotal(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
        $creditmemoCollection = $creditmemo->getOrder()->getCreditmemosCollection();
        if($creditmemoCollection->getSize() > 0 && $creditmemo->getOrder()->getDibsFee() > 0) {
            return false;
        } else {
            return true;
        } 
    }
}
