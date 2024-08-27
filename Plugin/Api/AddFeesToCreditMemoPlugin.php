<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Plugin\Api;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoSearchResultInterface;

class AddFeesToCreditMemoPlugin
{
    /**
     * @var CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    /**
     * AddFeesToCreditMemoPlugin constructor.
     *
     * @param CreditmemoExtensionFactory $creditmemoExtensionFactory
     */
    public function __construct(CreditmemoExtensionFactory $creditmemoExtensionFactory)
    {
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
    }

    /**
     * Set Fees Data
     *
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface $creditMemo
     * @return CreditmemoInterface
     */
    public function afterGet(
        CreditmemoRepositoryInterface $subject,
        CreditmemoInterface $creditMemo
    ) {
        /** @var \Magento\Sales\Api\Data\CreditmemoExtension $extensionAttributes */
        $extensionAttributes = $creditMemo->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->creditmemoExtensionFactory->create();
        }

        $extensionAttributes->setTerravivesFeeAmount($creditMemo->getTerravivesFeeAmount());
        $extensionAttributes->setBaseTerravivesFeeAmount($creditMemo->getBaseTerravivesFeeAmount());
        $extensionAttributes->setTerravivesFeeTaxAmount($creditMemo->getTerravivesFeeTaxAmount());
        $extensionAttributes->setBaseTerravivesFeeTaxAmount($creditMemo->getBaseTerravivesFeeTaxAmount());
        $extensionAttributes->setTerravivesFeeDetails($creditMemo->getTerravivesFeeDetails());

        $creditMemo->setExtensionAttributes($extensionAttributes);

        return $creditMemo;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoSearchResultInterface $creditMemoSearchResult
     * @return CreditmemoSearchResultInterface
     */
    public function afterGetList(
        CreditmemoRepositoryInterface $subject,
        CreditmemoSearchResultInterface $creditMemoSearchResult
    ) {
        /** @var CreditmemoInterface $creditMemo */
        foreach ($creditMemoSearchResult->getItems() as $creditMemo) {
            $this->afterGet($subject, $creditMemo);
        }

        return $creditMemoSearchResult;
    }
}
