<?php

namespace Dibs\Flexwin\Model\Sales\Order\Invoice;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Totals extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setPaymentCharge(0);
        $invoice->setBasePaymentCharge(0);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getOrder()->getDibsFee() / 100);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getOrder()->getBaseDibsFee() / 100);
        return $this;
    }
}