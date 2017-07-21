<?php

namespace Dibs\Flexwin\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Dibs\Flexwin\Model\Method;

class Refund extends Request implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $this->preRequestValidate($buildSubject);
        $find = ['login', 'password'];
        $replace = [
                    $this->config->getValue('api_user'),
                    $this->config->getValue('api_password')
                   ];
        $url = str_replace($find, $replace,Method::REFUND_URL_PATTERN);
        $merchant = $this->config->getValue('merchant');
        $transact = $this->payment->getLastTransId();
        $amount = Method::api_dibs_round($this->prepareAmount($buildSubject['amount']));
        $orderId = $this->payment->getOrder()->getIncrementId();
        $md5key = $this->getMD5keyForRefund($merchant, $orderId, $transact, $amount);

        return ['url' => $url,
                    'body' =>
                        ['merchant' => $merchant,
                            'amount' => $amount,
                            'transact' => $transact,
                            'md5key' => $md5key,
                            'orderid' => $orderId
                        ]
               ];
    }

    /**
     * @param string $merchant
     * @param string $orderId
     * @param int $transact
     * @param int $amount
     * @return string
     */
    public function getMD5keyForRefund($merchant, $orderId, $transact, $amount)
    {

        $key1 = $this->config->getValue(Method::KEY_MD5_KEY1_NAME);
        $key2 = $this->config->getValue(Method::KEY_MD5_KEY2_NAME);
        $parameterString = '';
        $parameterString .= Method::KEY_MERCHANT_NAME . '=' . $merchant;
        $parameterString .= '&' . Method::KEY_ORDERID_NAME . '=' . $orderId;
        $parameterString .= '&' . Method::KEY_TRANSACT_NAME . '=' . $transact;
        $parameterString .= '&' . Method::KEY_AMOUNT_NAME . '=' . $amount;
        $md5key = md5($key2 . md5($key1 . $parameterString));

        return $md5key;

    }
}
