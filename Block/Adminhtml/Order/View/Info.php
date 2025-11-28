<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order;
use Terravives\Fee\Helper\Price;
use Terravives\Fee\Helper\Data;

class Info extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Price
     */
    protected $helperPrice;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Info constructor.
     *
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param Price $helperPrice
     * @param Data $helperData
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context,
        Price $helperPrice,
        Data $helperData,
        Registry $registry,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->helperPrice = $helperPrice;
        $this->helperData = $helperData;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get Fee Details
     *
     * @return array|null
     */
    public function getFeeDetails()
    {
        $order = $this->getOrder();
        $baseFeeAmount = $order->getBaseTerravivesFeeAmount();

        if ($baseFeeAmount <= 0) {
            return null;
        }

        $feeDetails = [
            'fee_title' => __('Fee Amount'),
            'has_project' => false,
            'show_tax_separately' => false
        ];

        // Get fee details from order
        $detailsJson = $order->getTerravivesFeeDetails();
        $details = $detailsJson ? $this->serializer->unserialize($detailsJson) : [];

        // Check if tax is shown separately
        $showTaxSeparately = isset($details['show_tax_separately']) && $details['show_tax_separately'] === true;
        $feeDetails['show_tax_separately'] = $showTaxSeparately;

        if ($showTaxSeparately) {
            // When tax is separate, show base fee, tax, and total
            $taxAmount = $order->getBaseTerravivesFeeTax() ?: (isset($details['tax_amount']) ? $details['tax_amount'] : 0);
            $totalFee = $baseFeeAmount;
            $baseFee = $baseFeeAmount - $taxAmount;

            $feeDetails['base_fee'] = $this->helperPrice->getFormatPrice($baseFee);
            $feeDetails['tax_amount'] = $this->helperPrice->getFormatPrice($taxAmount);
            $feeDetails['total_fee'] = $this->helperPrice->getFormatPrice($totalFee);
            $feeDetails['tax_percentage'] = isset($details['tax_percentage']) ? $details['tax_percentage'] : '';
        } else {
            // When tax is not separate, show just the fee amount
            $feeDetails['fee_value'] = $this->helperPrice->getFormatPrice($baseFeeAmount);
        }

        // Check for project information
        if (isset($details['projects']) && isset($details['project_id'])) {
            $feeDetails['has_project'] = true;
            foreach ($details['projects'] as $project) {
                if ($project['id'] == $details['project_id']) {
                    $feeDetails['project_title'] = $project['title'];
                    $feeDetails['project_url'] = $project['url'];
                    break;
                }
            }
        }

        return $feeDetails;
    }

    /**
     * Retrieve order model
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }
}
