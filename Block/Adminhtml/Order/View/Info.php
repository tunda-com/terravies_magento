<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Block\Adminhtml\Order\View;

use Magento\Framework\Serialize\SerializerInterface;

class Info extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Terravives\Fee\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Info constructor.
     *
     * @param SerializerInterface $serializer
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Terravives\Fee\Helper\Price $helperPrice
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        \Magento\Backend\Block\Template\Context $context,
        \Terravives\Fee\Helper\Price $helperPrice,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->serializer   = $serializer;
        $this->helperPrice  = $helperPrice;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

    /**
     * Get Fee Details
     *
     * @return array | null
     */
    public function getFeeDetails()
    {
        $feeDetails = ['fee_title' => 'Fee Amount:'];
        $basePrice       = $this->getOrder()->getBaseTerravivesFeeAmount();

        if ($basePrice > 0) {
            $price                             = $this->helperPrice->getFormatPrice($basePrice);
            $feeDetails['fee_value'] = $price;

            if ($this->getOrder()->getTerravivesFeeDetails()) {
                $details = $this->serializer->unserialize($this->getOrder()->getTerravivesFeeDetails());
            }

            return $feeDetails;
        } else {
            return null;
        }
    }
}
