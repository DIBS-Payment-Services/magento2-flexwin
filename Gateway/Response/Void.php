<?php

namespace Dibs\Flexwin\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

class Void extends ResponseHandler implements HandlerInterface {
    
    public function handle(array $handlingSubject, array $response) {
        $this->preHandleValidate($handlingSubject);
        if($response['status'] == \Dibs\Flexwin\Model\Method::API_OPERATION_FAILURE) {
           $msg = __('Void declined');
           $msg .= $this->prepareErrorMessage($response['error']);
           throw new \Magento\Framework\Exception\LocalizedException(__($msg));
        }
        if($response['status'] == \Dibs\Flexwin\Model\Method::API_OPERATION_SUCCESS) {
            $this->message->addSuccess(__('Transaction was voided'));
        }
    }
}