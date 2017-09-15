<?php

namespace Dibs\Flexwin\Block\Sales;

use Dibs\Flexwin\Model\Method;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;
    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

     /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }
    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    }
    public function getStore()
    {
        return $this->_order->getStore();
    }
    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order;
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        if(!$parent->getSource()->getDibsFee()) {
            return $this;
        }
        $fee = new \Magento\Framework\DataObject(
            [
                'code'   => 'dibs_fee',
                'strong' => false,
                'value'  =>  $this->getOrder()->getDibsFee() / 100,
                'label'  => __('Dibs fee'),
            ]
        );
        $parent->addTotal($fee, 'dibs_fee');
        return $this;
    }
}
