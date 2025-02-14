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
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Info constructor.
     *
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param Price $helperPrice
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context,
        Price $helperPrice,
        Registry $registry,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->helperPrice = $helperPrice;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get Fee Details
     *
     * @return array | null
     */
    public function getFeeDetails()
    {
        $feeDetails = ['fee_title' => 'Fee Amount:'];
        $basePrice = $this->getOrder()->getBaseTerravivesFeeAmount();

        if ($basePrice > 0) {
            $price = $this->helperPrice->getFormatPrice($basePrice);
            $feeDetails['fee_value'] = $price;
            $feeDetails['has_project'] = false;
            if ($this->getOrder()->getTerravivesFeeDetails()) {
                $details = $this->serializer->unserialize($this->getOrder()->getTerravivesFeeDetails());
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
            }

            return $feeDetails;
        } else {
            return null;
        }
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
