<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Plugin\Klarna;

class AddFeesAsSurcharge
{
    /**
     * @param \Klarna\Core\Model\Api\Builder $subject
     * @param array $orderLines
     * @return array
     */
    public function afterGetOrderLines(\Klarna\Core\Model\Api\Builder $subject, array $orderLines = []): array
    {
        $quote = $subject->getObject();
        if (!$quote) {
            return $orderLines;
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        if (!$address) {
            return $orderLines;
        }

        $orderLines[] = [
            'type'             => 'surcharge',
            'reference'        => 'fees',
            'name'             => 'fees',
            'quantity'         => 1,
            'unit_price'       => $this->toApiFloat((float)$address->getBaseTerravivesFeeAmount()),
            'tax_rate'         => 0,
            'total_tax_amount' => 0,
            'total_amount'     => $this->toApiFloat((float)$address->getBaseTerravivesFeeAmount())
        ];

        return $orderLines;
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
