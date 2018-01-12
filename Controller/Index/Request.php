<?php

namespace Dibs\Flexwin\Controller\Index;

use Magento\Framework\DataObject;

class Request extends \Dibs\Flexwin\Controller\Index
{
    protected $formKeyValidator;

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Sales\Model\Order $order,
                                \Magento\Quote\Model\Quote $quote,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Dibs\Flexwin\Model\Method $method,
                                \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator) {
        parent::__construct($context, $order, $quote, $checkoutSession, $method);
        $this->formKeyValidator = $formKeyValidator;
    }

    public function execute()
    {
        if ($this->checkPost() && $this->formKeyValidator->validate($this->_request)) {
            $result = new DataObject();
            $response = $this->getResponse();
            $result->addData($this->method->collectRequestParams());
            return $response->representJson($result->toJson());
        }

    }
}
