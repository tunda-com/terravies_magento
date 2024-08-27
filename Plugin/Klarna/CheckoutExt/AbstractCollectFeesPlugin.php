<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Plugin\Klarna\CheckoutExt;

abstract class AbstractCollectFeesPlugin
{
    /**
     * @param \Klarna\Base\Model\Api\Parameter $parameter
     * @param \Klarna\Base\Model\Checkout\Orderline\DataHolder $dataHolder
     * @param float $amount
     */
    protected function collect(
        \Klarna\Base\Model\Api\Parameter $parameter,
        \Klarna\Base\Model\Checkout\Orderline\DataHolder $dataHolder,
        float $amount
    ) {
        $totals = $dataHolder->getTotals();

        if (!is_array($totals) || !isset($totals['terravives_fee'])) {
            return;
        }

        $total = $totals['terravives_fee'];

        if ($total->getValue() !== 0) {
            $value     = (float)$parameter->getSurchargeUnitPrice() + $this->toApiFloat($amount);
            $title     = $parameter->getSurchargeName();
            $title     = $title ? $title . ' ' . $total->getTitle()->getText() : $total->getTitle()->getText();
            $reference = $parameter->getSurchargeReference();
            $reference = $reference ? $reference . ' ' . $total->getCode() : $total->getCode();

            $parameter->setSurchargeUnitPrice($value)
                      ->setSurchargeTotalAmount($value)
                      ->setSurchargeName($title)
                      ->setSurchargeReference($reference);
        }
    }

    /**
     * Prepare float for API call
     *
     * @param float $float
     *
     * @return false|float
     */
    private function toApiFloat(float $float)
    {
        return round($float * 100);
    }
}
