<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class AddFeesToPaypal implements ObserverInterface
{
    const AMOUNT_SUBTOTAL = 'subtotal';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * AddProductFeesToPaypal constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        SerializerInterface $serializer
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->serializer      = $serializer;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cart  = $observer->getEvent()->getCart();
        $quote = $this->checkoutSession->getQuote();

        if ($cart instanceof \Magento\Paypal\Model\Cart &&
            method_exists($cart, 'addCustomItem')
        ) {
            $feePrice = $this->getFeeAmount($quote);

            if ($feePrice) {
                $cart->addCustomItem(__("Fee"), 1, $feePrice);
            }
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|null
     */
    protected function getFeeAmount(\Magento\Quote\Model\Quote $quote): ?float
    {
        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        if($address->getTerravivesFeeDetails()) {
            $feeDetails = $this->serializer->unserialize($address->getTerravivesFeeDetails());
        } else {
            return null;
        }

        return (float)$feeDetails['fee'];
    }
}
