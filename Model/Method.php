<?php

namespace Dibs\Flexwin\Model;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Invoice;
class Method
{
    protected $quote;
    protected $urlInterface;
    protected $paymentHelper;
    protected $_checkoutSession;
    protected $_quote = false;
    protected $methodObj;
    protected $request;
    protected $order;
    protected $orderSender;
    protected $scopeConfig;
    protected $invoiceService;
    protected $_objectManager;
    protected $registry;

    const KEY_CURRENCY_NAME    = 'currency';
    const KEY_MERCHANT_NAME    = 'merchant';
    const KEY_AMOUNT_NAME      = 'amount';
    const KEY_ACCEPTURL_NAME   = 'accepturl';
    const KEY_CANCELURL_NAME   = 'cancelurl';
    const KEY_PAYTYPE_NAME     = 'paytype';
    const KEY_CALLBACKURL_NAME = 'callbackurl';
    const KEY_ORDERID_NAME     = 'orderid';
    const KEY_DECORATOR_NAME   = 'decorator';
    const KEY_LANG_NAME        = 'lang';
    const KEY_TESTMODE_NAME    = 'testmode';
    const KEY_MD5KEY_NAME      = 'md5key';
    const KEY_CAPTURENOW_NAME  = 'capturenow';
    const KEY_MD5_KEY1_NAME    = 'md5key1';
    const KEY_MD5_KEY2_NAME    = 'md5key2';
    const KEY_CALCFEE_NAME     = 'calcfee';
    
    const RETURN_CONTEXT_ACCEPT   = 'accept';
    const RETURN_CONTEXT_CALLBACK = 'callback';

    const API_OPERATION_SUCCESS = 'ACCEPTED';
    const API_OPERATION_FAILURE = 'DECLINED';
    
    const CAPTURE_URL = 'https://payment.architrade.com/cgi-bin/capture.cgi';
    
    const REFUND_URL_PATTERN = 'https://login:password@payment.architrade.com/cgi-adm/refund.cgi';
    
    public function __construct(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\UrlInterface $urlInterface,
        PaymentHelper $paymentHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\ObjectManagerInterface $_objectManager
    ) {
        $this->quote = $quote;
        $this->urlInterface = $urlInterface;
        $this->paymentHelper = $paymentHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->methodObj = $this->paymentHelper->getMethodInstance(ConfigProvider::METHOD_CODE);
        $this->request = $request;
        $this->order = $order;
        $this->orderSender = $orderSender;
        $this->scopeConfig = $scopeConfig;
        $this->invoiceService = $invoiceService;
        $this->_objectManager = $_objectManager;
    }

    /**
     * Collect params that will be
     * sended to DIBS Payment gateway
     *
     * @return array
     */
    public function collectRequestParams()
    {
        $order = $this->order->load($this->request->getParam(self::KEY_ORDERID_NAME));

        if ($order->getId()) {
            $orderId = $order->getIncrementId();
            $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $this->setCustomOrderStatus('order_status_pending');
            $order->save();
           
            $requestParams['result'] = 'success';
            $requestParams['params'] = array(
                self::KEY_MERCHANT_NAME    => trim($this->methodObj->getConfigData(self::KEY_MERCHANT_NAME)),
                self::KEY_ORDERID_NAME     => $orderId,
                self::KEY_CURRENCY_NAME    => $order->getOrderCurrencyCode(),
                self::KEY_AMOUNT_NAME      => $this->api_dibs_round($order->getGrandTotal()),
                self::KEY_ACCEPTURL_NAME   => $this->urlInterface->getDirectUrl('dibsflexwin/index/accept'),
                self::KEY_CANCELURL_NAME   => $this->urlInterface->getDirectUrl('dibsflexwin/index/cancel'),
                self::KEY_PAYTYPE_NAME     => $this->request->getParam(self::KEY_PAYTYPE_NAME),
                self::KEY_CALLBACKURL_NAME => $this->urlInterface->getDirectUrl('dibsflexwin/index/callback')
            );

            if ($this->methodObj->getConfigData(self::KEY_TESTMODE_NAME) == 1) {
                $requestParams['params']['test'] = 1;
            }
            if ($this->methodObj->getConfigData(self::KEY_DECORATOR_NAME)) {
                $requestParams['params'][self::KEY_DECORATOR_NAME] = $this->methodObj->getConfigData(self::KEY_DECORATOR_NAME);
            }
            if ($this->methodObj->getConfigData(self::KEY_LANG_NAME)) {
                $langCode = $this->scopeConfig->getValue('payment/' . ConfigProvider::METHOD_CODE . 
                        '/' . self::KEY_LANG_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORES);
                $requestParams['params'][self::KEY_LANG_NAME] = $langCode;
            }
            if ($this->methodObj->getConfigData(self::KEY_CALCFEE_NAME) == 1) {
                $requestParams['params'][self::KEY_CALCFEE_NAME] = 'yes';
            }
            $macCodeParams = array(
                self::KEY_MERCHANT_NAME => $requestParams['params'][self::KEY_MERCHANT_NAME],
                self::KEY_ORDERID_NAME  => $requestParams['params'][self::KEY_ORDERID_NAME],
                self::KEY_CURRENCY_NAME => $requestParams['params'][self::KEY_CURRENCY_NAME],
                self::KEY_AMOUNT_NAME   => $requestParams['params'][self::KEY_AMOUNT_NAME]
            );

            if ($md5Key = $this->calcMd5Code($macCodeParams)) {
                $requestParams['params'][self::KEY_MD5KEY_NAME] = $md5Key;
            }

            if ($this->methodObj->getConfigData(self::KEY_CAPTURENOW_NAME) == '1') {
                $requestParams['params'][self::KEY_CAPTURENOW_NAME] = 1;
            }

        } else {
            $requestParams['result'] = 'error';
            $requestParams['message'] = 'error occured';
        }

        return $requestParams;
    }

    /**
     * Calculate md5key from given params
     *
     * @param type $params
     *
     * @return String
     */
    protected function calcMd5Code($params)
    {
        $md5key = '';
        if ($this->checkMd5KeyCodeRequired()) {
            $key1 = trim($this->methodObj->getConfigData(self::KEY_MD5_KEY1_NAME));
            $key2 = trim($this->methodObj->getConfigData(self::KEY_MD5_KEY2_NAME));
            $parameter_string = '';
            $parameter_string .= self::KEY_MERCHANT_NAME . '=' . $params[self::KEY_MERCHANT_NAME];
            $parameter_string .= '&' . self::KEY_ORDERID_NAME . '=' . $params[self::KEY_ORDERID_NAME];
            $parameter_string .= '&' . self::KEY_CURRENCY_NAME . '=' . $params[self::KEY_CURRENCY_NAME];
            $parameter_string .= '&' . self::KEY_AMOUNT_NAME . '=' . $params[self::KEY_AMOUNT_NAME];
            $md5key = md5($key2 . md5($key1 . $parameter_string));
        }
        return $md5key;
    }

    /**
     * Set returned params to order for
     * success return and callback, add
     * comment for order for successreturn/callback cases
     *
     *
     * @param type $context
     *
     * @return $order
     */
    protected function setReturnedParamsToOrder($context = self::RETURN_CONTEXT_ACCEPT)
    {
        $orderComment = '';
        if ($context == self::RETURN_CONTEXT_ACCEPT) {
            $orderComment = __('Customer successfully returned from DIBS');
        }
        if ($context == self::RETURN_CONTEXT_CALLBACK) {
            $orderComment = __('Callback was received from DIBS');
        }
        $orderIncrementId = $this->request->getParam(self::KEY_ORDERID_NAME);
        $order = $this->order->loadByIncrementId($orderIncrementId);
        $transactId = $this->request->getParam('transact');
        if ($order->getId()) {
            $order->addStatusHistoryComment($orderComment);
            $order->save();
            $payment = $order->getPayment();
            $payment->setLastTransId($transactId);
            $payment->save();

        }
        return $order;
    }

    /**
     * Complete checkout and set new status to order
     * \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW;
     * Send Email comfirmation
     *
     * @param type $context
     *
     * @return type
     */
    public function completeCheckout($context = self::RETURN_CONTEXT_ACCEPT)
    {
        if ($this->checkMd5KeyCodeRequired()) {
            $returnedParams = array(
                self::KEY_MERCHANT_NAME => $this->request->getParam(self::KEY_MERCHANT_NAME),
                self::KEY_ORDERID_NAME  => $this->request->getParam(self::KEY_ORDERID_NAME),
                self::KEY_CURRENCY_NAME => $this->request->getParam(self::KEY_CURRENCY_NAME),
                self::KEY_AMOUNT_NAME   => $this->request->getParam(self::KEY_AMOUNT_NAME),
                self::KEY_MD5KEY_NAME   => $this->request->getParam(self::KEY_MD5KEY_NAME),
            );

            if (!$this->checkMacCode($returnedParams)) {
                // add logging of fail mac code

                return;
            }
        }
        $order = $this->setReturnedParamsToOrder($context);
        if (!$order->getEmailSent()) {
            $this->orderSender->send($order);
        }
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
        $this->setCustomOrderStatus('order_status');
        if($this->shouldMakeInvoice() && $order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $payment = $order->getPayment();
            $this->capture($invoice, $payment);
        } 
        $order->setIsNotified(false);
        $order->save();
    }

    public static function api_dibs_round($fNum, $iPrec = 2)
    {
        return empty($fNum) ? (int) 0 : (int) (string)
                (round($fNum, $iPrec) * pow(10, $iPrec));
    }

    /**
     *  Compare calculated mac
     *  code based on returned params
     *  and returned mac code
     *
     * @param type $returnedParams
     * @return boolean
     */
    protected function checkMacCode(array $returnedParams)
    {
       return ($this->calcMd5Code($returnedParams) ==
               $returnedParams[self::KEY_MD5KEY_NAME]) ? true : false;
    }

    public function setOrderCancelled($orderid)
    {
        $order = $this->order->loadByIncrementId($orderid);
        if ($order->getId()) {
            $order->cancel();
            $this->setCustomOrderStatus('order_status_cancel');
            $order->addStatusHistoryComment(__('Customer has cancelled payment'));
            $order->save();
        }

    }

    public function restoreQuoteFromOrder($orderid)
    {
        $order = $this->order->loadByIncrementId($orderid);
        if ($order->getId()) {
            $quote = $this->quote->loadByIdWithoutStore($order->getQuoteId());
            $quote->setIsActive(1)->setReservedOrderId(null)->save();
            $this->_checkoutSession->replaceQuote($quote);
        }
    }

    /**
     * Check if we have to check md5key only
     * in case if we set up md5key1 md5key2 in admin
     *
     * @return bool
     */
    protected function checkMd5KeyCodeRequired()
    {
        return trim($this->methodObj->getConfigData(self::KEY_MD5_KEY1_NAME)) &&
        trim($this->methodObj->getConfigData(self::KEY_MD5_KEY2_NAME)) ? true : false;

    }

    /**
     * Set custom order staus for DIBS for states:
     *  pending_payment, processing, cancelled
     *
     * @param string $statusConfName
     *
     * @return type
     */

    protected function setCustomOrderStatus($statusConfName)
    {
        $orderStatus = $this->methodObj->getConfigData($statusConfName);
        if ($orderStatus) {
            $this->order->setStatus($orderStatus);
        }
    }

    protected function capture($invoice, $payment) {
         try {
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_OFFLINE);
                $invoice->setTransactionId($payment->getLastTransId());
                $invoice->register();
                $transactionSave = $this->_objectManager->create(
                    'Magento\Framework\DB\Transaction'
                )->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();
            } catch(\Exception $e) {
                // catch and continue
            }
    }

    protected function updateTotals(OrderPaymentInterface $payment, $data)
    {
        foreach ($data as $key => $amount) {
            if (null !== $amount) {
                $was = $payment->getDataUsingMethod($key);
                $payment->setDataUsingMethod($key, $was + $amount);
            }
        }
    }

    protected function shouldMakeInvoice() {
        return (null !== $this->request->getParam('capturenow'))
                && ($this->request->getParam('capturenow') == 1)
                && ($this->methodObj->getConfigData('makeinvoice') == 1)
                ? true : false;
    }

}
