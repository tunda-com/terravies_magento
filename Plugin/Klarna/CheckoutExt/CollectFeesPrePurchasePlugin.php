<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Plugin\Klarna\CheckoutExt;

class CollectFeesPrePurchasePlugin extends AbstractCollectFeesPlugin
{
    /**
     * @param \Klarna\Base\Model\Checkout\Orderline\Items\Surcharge $subject
     * @param \Klarna\Base\Model\Checkout\Orderline\Items\Surcharge $result
     * @param \Klarna\Base\Model\Api\Parameter $parameter
     * @param \Klarna\Base\Model\Checkout\Orderline\DataHolder $dataHolder
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Klarna\Base\Model\Checkout\Orderline\Items\Surcharge
     */
    public function afterCollectPrePurchase(
        \Klarna\Base\Model\Checkout\Orderline\Items\Surcharge $subject,
        \Klarna\Base\Model\Checkout\Orderline\Items\Surcharge $result,
        \Klarna\Base\Model\Api\Parameter $parameter,
        \Klarna\Base\Model\Checkout\Orderline\DataHolder $dataHolder,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        if ($address) {
            $this->collect($parameter, $dataHolder, (float)$address->getBaseTerravivesFeeAmount());
        }

        return $result;
    }
}
