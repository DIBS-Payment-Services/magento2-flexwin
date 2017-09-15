<?php

namespace Dibs\Flexwin\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    
    public static function convertFeeAmount( $feeAmount ) {
        return $feeAmount / 100;
    }
}
