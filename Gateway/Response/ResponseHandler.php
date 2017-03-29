<?php

namespace Dibs\Flexwin\Gateway\Response;

use Magento\Framework\Message\ManagerInterface;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class ResponseHandler {

    protected $message;

    public function __construct(ManagerInterface $message) {
        $this->message = $message;
    }

    public function prepareErrorMessage(array $error) {
       $message = '';
       if(isset($error['message'])) {
           $message .= '<br>Error: <i>' . $error['message'] . '</i>';
       }
       if(isset($error['reason'])) {
           $message .= '<br>Decline reason=' . $error['reason'];
       }
       return $message;
    }

    protected function preHandleValidate(array $handlingSubject) {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }
    }

    public function handleTransaction(PaymentDataObject $payment) { 
       $payment = $payment->getPayment();
       $payment->setTransactionId($payment->getLastTransId());
       $payment->setIsTransactionClosed(false);
    }
}
