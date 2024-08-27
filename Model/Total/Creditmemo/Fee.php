<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model\Total\Creditmemo;

class Fee extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getTerravivesFeeAmount() > 0 && $order->getTerravivesFeeRefunded() <
            $order->getTerravivesFeeInvoiced()
        ) {
            $creditmemo->setTerravivesFeeAmount(
                $order->getTerravivesFeeInvoiced() -
                $order->getTerravivesFeeRefunded()
            );
            $creditmemo->setBaseTerravivesFeeAmount(
                $order->getBaseTerravivesFeeInvoiced() -
                $order->getBaseTerravivesFeeRefunded()
            );
            $creditmemo->setTerravivesFeeDetails($order->getTerravivesFeeDetails());

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getTerravivesFeeAmount());
            $creditmemo->setBaseGrandTotal(
                $creditmemo->getBaseGrandTotal() +
                $creditmemo->getBaseTerravivesFeeAmount()
            );
        } else {
            $creditmemo->setTerravivesFeeAmount(0);
            $creditmemo->setBaseTerravivesFeeAmount(0);
            $creditmemo->setTerravivesFeeDetails('');
        }

        return $this;
    }
}
