<?php

namespace Dibs\Flexwin\Model\Config\Source;

class Cancel extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string
     */
    protected $_stateStatuses = \Magento\Sales\Model\Order::STATE_CANCELED;
}
