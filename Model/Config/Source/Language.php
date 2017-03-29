<?php

namespace Dibs\Flexwin\Model\Config\Source;

class Language implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => 'da', 'label' => __('Danish')],
            ['value' => 'en', 'label' => __('English')],
            ['value' => 'de', 'label' => __('German')],
            ['value' => 'es', 'label' => __('Spanish')],
            ['value' => 'fi', 'label' => __('Finnish')],
            ['value' => 'fo', 'label' => __('Faroese')],
            ['value' => 'fr', 'label' => __('French')],
            ['value' => 'it', 'label' => __('Italian')],
            ['value' => 'nl', 'label' => __('Dutch')],
            ['value' => 'no', 'label' => __('Norwegian')],
            ['value' => 'pl', 'label' => __('Polish')],
            ['value' => 'sv', 'label' => __('Swedish')],
            ['value' => 'kl', 'label' => __('Greenlandic')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
