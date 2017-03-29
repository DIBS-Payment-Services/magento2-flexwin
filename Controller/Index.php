<?php

namespace Dibs\Flexwin\Controller;

abstract class Index extends \Magento\Framework\App\Action\Action
{
    protected $order;
    protected $quote;
    protected $checkoutSession;
    protected $method;
  
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Dibs\Flexwin\Model\Method $method
        
    ) {
        parent::__construct($context);
        $this->order = $order;
        $this->quote = $quote;
        $this->checkoutSession = $checkoutSession;
        $this->method = $method;
    }

    public function checkPost()
    {
       return $this->getRequest()->isPost() &&
              $this->getRequest()->getParam(\Dibs\Flexwin\Model\Method::KEY_ORDERID_NAME) ? true : false;
    }
}