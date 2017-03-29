<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dibs\Flexwin\Model\Config\Backend;

class Makeinvoice extends \Magento\Framework\App\Config\Value
{
    protected $requestData;

    public function beforeSave()
    {
        if($this->getFieldsetDataValue('capturenow') == 0 && $this->getValue() == 1) {
            $field = $this->getFieldConfig();
            $label = $field && is_array($field) ? $field['label'] : 'value';
            $msg = __('Invalid %1. %2', $label, 'For enabling this feature, \'Capturenow\' must be enabled');
            throw new \Magento\Framework\Exception\LocalizedException($msg);
        }

    }

}
