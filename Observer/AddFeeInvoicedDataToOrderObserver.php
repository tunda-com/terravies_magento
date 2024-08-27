<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddFeeInvoicedDataToOrderObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order   = $observer->getEvent()->getOrder();
        $invoice = $observer->getEvent()->getInvoice();

        if ($invoice->getBaseTerravivesFeeAmount() > 0) {
            $order->setTerravivesFeeInvoiced(
                $order->getTerravivesFeeInvoiced() +
                $invoice->getTerravivesFeeAmount()
            );
            $order->setBaseTerravivesFeeInvoiced(
                $order->getBaseTerravivesFeeInvoiced() +
                $invoice->getBaseTerravivesFeeAmount()
            );
        }
    }
}
