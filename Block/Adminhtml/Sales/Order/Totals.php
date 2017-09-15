<?php

namespace Dibs\Flexwin\Block\Adminhtml\Sales\Order;




class Totals extends \Dibs\Flexwin\Block\Adminhtml\Sales\TotalsAbstract {  //\Magento\Framework\View\Element\Template {
    
   
   public function initTotals()
    {
        if(!$this->getOrder()->getDibsFee()) {
            return $this;
        }
        $total = $this->createDibsTotalFee();
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
