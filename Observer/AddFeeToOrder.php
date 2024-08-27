<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Terravives\Fee\Model\Fee;

class AddFeeToOrder implements ObserverInterface
{
    /**
     * @var Fee
     */
    protected $fee;

    /**
     * AddFeeToOrder constructor.
     *
     * @param Fee $fee
     */
    public function __construct(Fee $fee)
    {
        $this->fee = $fee;
    }

    /**
     * Add fee data to order data
     *
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();

        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        if ($address->getTerravivesFeeAmount()) {
            $order->setTerravivesFeeAmount($address->getTerravivesFeeAmount());
            $order->setBaseTerravivesFeeAmount($address->getBaseTerravivesFeeAmount());
            $order->setTerravivesFeeDetails($address->getTerravivesFeeDetails());
        }

        return $this;
    }
}
