<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model\Total\Invoice;

class Fee extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();

        if ($order->getTerravivesFeeAmount() > 0 && $order->getTerravivesFeeInvoiced() <
            ($order->getTerravivesFeeAmount() - $order->getTerravivesFeeCanceled())
        ) {
            $invoice->setTerravivesFeeAmount(
                $order->getTerravivesFeeAmount() -
                $order->getTerravivesFeeInvoiced() - $order->getTerravivesFeeCanceled()
            );
            $invoice->setBaseTerravivesFeeAmount(
                $order->getBaseTerravivesFeeAmount() -
                $order->getBaseTerravivesFeeInvoiced() - $order->getBaseTerravivesFeeCanceled()
            );
            $invoice->setTerravivesFeeDetails($order->getTerravivesFeeDetails());

            $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getTerravivesFeeAmount());
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getBaseTerravivesFeeAmount());
        } else {
            $invoice->setTerravivesFeeAmount(0);
            $invoice->setBaseTerravivesFeeAmount(0);
            $invoice->setTerravivesFeeDetails('');
        }

        return $this;
    }
}
