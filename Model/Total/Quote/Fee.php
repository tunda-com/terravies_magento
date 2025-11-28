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
            $basePrice = $feeDetails['fee'];
        }

        $storeId = $quote->getStoreId();

        // Calculate fee with tax breakdown
        $feeCalculation = $this->helperData->calculateFeeWithTax($basePrice, $storeId);

        // Store tax information in fee details for frontend display
        $feeDetails['show_tax_separately'] = $this->helperData->showTaxSeparately($storeId);
        $feeDetails['tax_amount'] = $feeCalculation['tax_amount'];
        $feeDetails['base_fee_amount'] = $feeCalculation['base_fee'];
        $feeDetails['tax_percentage'] = $this->helperData->getTaxPercentage($storeId);
        $feeDetails['total_fee'] = $feeCalculation['total_fee'];

        if ($this->helperData->showTaxSeparately($storeId)) {
            // When showing separately, add base fee and tax separately
            $terravivesFeeAmount     = $this->priceCurrency->convertAndRound($feeCalculation['base_fee'], $quote->getStore());
            $baseTerravivesFeeAmount = $this->priceCurrency->round($feeCalculation['base_fee']);

            $terravivesFeeTax     = $this->priceCurrency->convertAndRound($feeCalculation['tax_amount'], $quote->getStore());
            $baseTerravivesFeeTax = $this->priceCurrency->round($feeCalculation['tax_amount']);

            // Store fee amounts (for display and saving to order)
            $total->setTerravivesFeeAmount($terravivesFeeAmount);
            $address->setTerravivesFeeAmount($terravivesFeeAmount);
            $total->setBaseTerravivesFeeAmount($baseTerravivesFeeAmount);
            $address->setBaseTerravivesFeeAmount($baseTerravivesFeeAmount);

            // Store tax amounts (for display and saving to order)
            $total->setTerravivesFeeTax($terravivesFeeTax);
            $address->setTerravivesFeeTax($terravivesFeeTax);
            $total->setBaseTerravivesFeeTax($baseTerravivesFeeTax);
            $address->setBaseTerravivesFeeTax($baseTerravivesFeeTax);

            // Add base fee to totals (this adds to grand total automatically)
            $total->setTotalAmount('terravives_fee', $terravivesFeeAmount);
            $total->setBaseTotalAmount('terravives_fee', $baseTerravivesFeeAmount);

            // Manually add tax to grand total (not to Magento's tax line, just to grand total)
            $total->addTotalAmount('terravives_fee', $terravivesFeeTax);
            $total->addBaseTotalAmount('terravives_fee', $baseTerravivesFeeTax);
        } else {
            // When not separating, add the full amount
            $terravivesFeeAmount     = $this->priceCurrency->convertAndRound($basePrice, $quote->getStore());
            $baseTerravivesFeeAmount = $this->priceCurrency->round($basePrice);

            // Store fee amounts
            $total->setTerravivesFeeAmount($terravivesFeeAmount);
            $address->setTerravivesFeeAmount($terravivesFeeAmount);
            $total->setBaseTerravivesFeeAmount($baseTerravivesFeeAmount);
            $address->setBaseTerravivesFeeAmount($baseTerravivesFeeAmount);

            // Add to totals (this adds to grand total automatically)
            $total->setTotalAmount('terravives_fee', $terravivesFeeAmount);
            $total->setBaseTotalAmount('terravives_fee', $baseTerravivesFeeAmount);
        }

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
        $feeDetailsArray = $feeDetails ? $this->serializer->unserialize($feeDetails) : [];

        if ($address->getTerravivesFeeAmount() && $feeDetails) {
            return [
                'code'                      => $this->getCode(),
                'title'                     => __('Fee'),
                'value'                     => $address->getTerravivesFeeAmount(),
                'terravives_fee_details'    => $feeDetailsArray
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
}
