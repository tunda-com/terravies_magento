<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Plugin\Api;

use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class AddFeesToOrderPlugin
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * AddFeesToOrderPlugin constructor.
     *
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * Set Fees Data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    )
    {
        /** @var OrderExtension $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $extensionAttributes->setTerravivesFeeInvoiced($order->getTerravivesFeeInvoiced());
        $extensionAttributes->setBaseTerravivesFeeInvoiced($order->getBaseTerravivesFeeInvoiced());
        $extensionAttributes->setTerravivesFeeRefunded($order->getTerravivesFeeRefunded());
        $extensionAttributes->setBaseTerravivesFeeRefunded($order->getBaseTerravivesFeeRefunded());
        $extensionAttributes->setTerravivesFeeCancelled($order->getTerravivesFeeCancelled());
        $extensionAttributes->setBaseTerravivesFeeCancelled($order->getBaseTerravivesFeeCancelled());
        $extensionAttributes->setTerravivesFeeAmount($order->getTerravivesFeeAmount());
        $extensionAttributes->setBaseTerravivesFeeAmount($order->getBaseTerravivesFeeAmount());
        $extensionAttributes->setTerravivesFeeTaxAmount($order->getTerravivesFeeTaxAmount());
        $extensionAttributes->setBaseTerravivesFeeTaxAmount($order->getBaseTerravivesFeeTaxAmount());
        $extensionAttributes->setTerravivesFeeDetails($order->getTerravivesFeeDetails());

        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $orderSearchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $orderSearchResult
    ) {
        /** @var OrderInterface $order */
        foreach ($orderSearchResult->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $orderSearchResult;
    }
}
