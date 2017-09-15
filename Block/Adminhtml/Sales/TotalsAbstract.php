<?php

namespace Dibs\Flexwin\Block\Adminhtml\Sales;

 abstract class TotalsAbstract extends \Magento\Framework\View\Element\Template {
    
    protected $_currency;
  
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_currency = $currency;
    }
    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }
    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }
   
    public abstract function initTotals();
    
    
    public function createDibsTotalFee() {
          return new \Magento\Framework\DataObject(
            [
                'code' => 'dibs_fee',
                'value' =>  $this->getOrder()->getDibsFee() / 100,
                'label' => __('Dibs fee'),
            ]
        );
        
    }
    
}
