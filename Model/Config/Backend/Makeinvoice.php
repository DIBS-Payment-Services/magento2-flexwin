<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dibs\Flexwin\Model\Config\Backend;

/**
 * Class Makeinvoice
 * @package Dibs\Flexwin\Model\Config\Backend
 */
class Makeinvoice extends \Magento\Framework\App\Config\Value
{
    /**
     * @var
     */
    protected $requestData;

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if (0 === $this->getFieldsetDataValue('capturenow') && 1 == $this->getValue()) {
            $field = $this->getFieldConfig();
            $label = $field && is_array($field) ? $field['label'] : 'value';
            $msg = __('Invalid %1. %2', $label, 'For enabling this feature, \'Capturenow\' must be enabled');
            throw new \Magento\Framework\Exception\LocalizedException($msg);
        }

    }
}
