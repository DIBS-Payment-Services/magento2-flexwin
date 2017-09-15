<?php

namespace Dibs\Flexwin\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Payment\Helper\Data as PaymentHelper;


class Request {

    /**
    * @var ConfigInterface
    */
    protected $config;
    protected $storeManager;
    protected $payment;
    protected $method;
    protected $paymentHelper;
    protected $subjectReader;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        \Dibs\Flexwin\Model\Method $method,
        PaymentHelper $paymentHelper,
        \Magento\Payment\Gateway\Helper\SubjectReader $subjectReader
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->method = $method;
        $this->paymentHelper = $paymentHelper;
        $this->subjectReader = $subjectReader;
    }

    protected function preRequestValidate(array $buildSubject) {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();
        $this->payment = $payment;
        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }
    }

    /**
     * Convert amount in base currency to 
     * amount in current currency 
     *  
     * @param type $amount
     * @return type
     */
    protected function prepareAmount($amount) {
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $currency = $this->storeManager->getStore()->getBaseCurrency();
        return $currency->convert($amount, $currencyCode);
    }
}
