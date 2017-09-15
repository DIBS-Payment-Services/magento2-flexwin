<?php

namespace Dibs\Flexwin\Block\Adminhtml\Sales\Order\Creditmemo;

class Totals extends \Dibs\Flexwin\Block\Adminhtml\Sales\TotalsAbstract
{
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        if(!$this->addDibsFee()) {
            return $this;
        }
        $fee = $this->createDibsTotalFee();
        $this->getParentBlock()->addTotalBefore($fee, 'grand_total');
        return $this;
    }
    
    protected function addDibsFee() {
        $creditmemoCollection = $this->getOrder()->getCreditmemosCollection();
        if($creditmemoCollection->getSize() > 0 && $this->getOrder()->getDibsFee() > 0) {
            return false;
        } else if ($this->getOrder()->getDibsFee() > 0) {
            return true;
        } 
    }
}
