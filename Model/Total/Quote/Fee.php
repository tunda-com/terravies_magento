<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model\Total\Quote;

use Magento\Tax\Model\Config as TaxConfig;
use Magento\Framework\Serialize\SerializerInterface;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Terravives\Fee\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Terravives\Fee\Helper\Fee
     */
    protected $helperFee;

    /**
     * @var bool
     */
    protected $isCollected;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Fee constructor.
     *
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Terravives\Fee\Helper\Data $helperData
     * @param \Terravives\Fee\Helper\Fee $helperFee
     */
    public function __construct(
        SerializerInterface $serializer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Terravives\Fee\Helper\Data $helperData,
        \Terravives\Fee\Helper\Fee $helperFee
    ) {
        $this->setCode('terravives_fee');
        $this->serializer     = $serializer;
        $this->eventManager   = $eventManager;
        $this->storeManager   = $storeManager;
        $this->priceCurrency  = $priceCurrency;
        $this->helperData     = $helperData;
        $this->helperFee = $helperFee;
    }

    /**
     * Collect address fee amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();

        if ($this->checkShipping($address, $shippingAssignment)) {
            return $this;
        }

        $feeDetails = $address->getTerravivesFeeDetails();
        $feeDetails = $feeDetails ? $this->serializer->unserialize($feeDetails) : [];
        if (empty($feeDetails) && $address->getBaseTerravivesFeeAmount() <= 0) {
            return $this;
        }

        $basePrice = 0;

        if (!empty($feeDetails['fee'])) {
            $basePrice                               = $feeDetails['fee'];
        }

        $terravivesFeeAmount     = $this->priceCurrency->convertAndRound($basePrice, $quote->getStore());
        $baseTerravivesFeeAmount = $this->priceCurrency->round($basePrice);

        $this->addPricesToAddress($total, $address, $terravivesFeeAmount);
        $this->addBasePricesToAddress($total, $address, $baseTerravivesFeeAmount);
        $this->addFeeDetailsToAddress($total, $address, $feeDetails);

        $this->isCollected = true;

        return $this;
    }

    /**
     * Add fee total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if (!$this->isCollected) {
            $quote->collectTotals();
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        if (!$address) {
            return null;
        }

        $feeDetails = $address->getTerravivesFeeDetails();

        if ($address->getTerravivesFeeAmount() && $feeDetails) {
            return [
                'code'                      => $this->getCode(),
                'title'                     => __('Fee'),
                'value'                     => $address->getTerravivesFeeAmount(),
                'terravives_fee_details' => $this->serializer->unserialize($feeDetails)
            ];
        }

        return null;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     *
     * @return bool
     */
    protected function checkShipping($address, $shippingAssignment)
    {
        if ($address->getSubtotal() == 0) {
            return true;
        }

        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param array $feeData
     *
     * @return $this
     */
    protected function addFeeDetailsToAddress($total, $address, $feeData)
    {
        $feeData = empty($feeData) ? '' : $this->serializer->serialize($feeData);
        $address->setTerravivesFeeDetails($feeData);
        $total->setTerravivesFeeDetails($feeData);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param double $terravivesFeeAmount
     *
     * @return $this
     */
    protected function addPricesToAddress($total, $address, $terravivesFeeAmount)
    {
        $total->setTerravivesFeeAmount($terravivesFeeAmount);
        $total->setTotalAmount('terravives_fee', $terravivesFeeAmount);

        $address->setTerravivesFeeAmount($terravivesFeeAmount);
        $address->setTotalAmount('terravives_fee', $terravivesFeeAmount);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param float $baseTerravivesFeeAmount
     *
     * @return $this
     */
    protected function addBasePricesToAddress($total, $address, $baseTerravivesFeeAmount)
    {
        $total->setBaseTerravivesFeeAmount($baseTerravivesFeeAmount);
        $total->setBaseTotalAmount('terravives_fee', $baseTerravivesFeeAmount);

        $address->setBaseTerravivesFeeAmount($baseTerravivesFeeAmount);
        $address->setBaseTotalAmount('terravives_fee', $baseTerravivesFeeAmount);

        return $this;
    }
}
