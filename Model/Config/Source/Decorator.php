<?php

namespace Dibs\Flexwin\Model\Config\Source;

class Decorator implements \Magento\Framework\Option\ArrayInterface
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
            ['value' => 'default', 'label' => __('Default')],
            ['value' => 'basal', 'label' => __('Basal')],
            ['value' => 'rich', 'label' => __('Rich')],
            ['value' => 'responsive', 'label' => __('Responsive')],
        ];
    }
}
