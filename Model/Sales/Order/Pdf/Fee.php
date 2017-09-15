<?php
 
namespace Dibs\Flexwin\Model\Sales\Order\Pdf;
 
class Fee extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    public function canDisplay() {
        if( $this->getSource()->getDibsFee()) {
             return true;
        } 
        return false;
    }
    
    public function getTotalsForDisplay()
    {
         $amount = $this->getOrder()->formatPriceTxt($this->getSource()->getDibsFee() / 100);
         if ($this->getAmountPrefix()) {
             $amount = $this->getAmountPrefix() . $amount;
         }
         $label = __('Dibs fee') . ':';
         $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
         $total = ['amount' => $amount, 'label' => $label, 'font_size' => $fontSize];
         return [$total];
    }
}
