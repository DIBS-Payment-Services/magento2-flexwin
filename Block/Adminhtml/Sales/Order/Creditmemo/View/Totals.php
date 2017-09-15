<?php

namespace Dibs\Flexwin\Block\Adminhtml\Sales\Order\Creditmemo\View;;

class Totals extends \Dibs\Flexwin\Block\Adminhtml\Sales\TotalsAbstract {
   
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        if(!$this->getParentBlock()->getSource()->getDibsFee()) {
            return $this;
        }
        $fee = $this->createDibsTotalFee();
        $this->getParentBlock()->addTotalBefore($fee, 'grand_total');
        return $this;
    }
    
}
