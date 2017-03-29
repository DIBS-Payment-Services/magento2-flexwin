<?php

namespace Dibs\Flexwin\Observer;

use Magento\Framework\Event\ObserverInterface;

class availabilityValidator implements ObserverInterface
{
    protected $config;

    public function __construct(\Dibs\Flexwin\Model\ConfigProvider $config) {
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $methodInstance = $observer['method_instance'];
        if($methodInstance->getCode() == \Dibs\Flexwin\Model\ConfigProvider::METHOD_CODE) {
            // Don not show this payment methos if all paytypes is disabled
            $arr = $this->config->getConfig();
            $arr = $arr['payment']['dibsFlexwin']['paytype'];
            $enabledPaytypes = array_filter($arr, function ($a) {return $a['enabled'] == 1;});
            if(!$enabledPaytypes) {
              $checkResult = $observer['result'];
              $checkResult->setData('is_available', false);
            }
        }
    }
}
