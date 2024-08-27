<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Plugin\Api;

use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceSearchResultInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class AddFeesToInvoicePlugin
{
    /**
     * @var InvoiceExtensionFactory
     */
    private $invoiceExtensionFactory;

    /**
     * AddFeesToInvoicePlugin constructor.
     *
     * @param InvoiceExtensionFactory $invoiceExtensionFactory
     */
    public function __construct(InvoiceExtensionFactory $invoiceExtensionFactory)
    {
        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
    }

    /**
     * Set Fees Data
     *
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface
     */
    public function afterGet(
        InvoiceRepositoryInterface $subject,
        InvoiceInterface $invoice
    ) {
        /** @var \Magento\Sales\Api\Data\InvoiceExtension $extensionAttributes */
        $extensionAttributes = $invoice->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->invoiceExtensionFactory->create();
        }

        $extensionAttributes->setTerravivesFeeAmount($invoice->getTerravivesFeeAmount());
        $extensionAttributes->setBaseTerravivesFeeAmount($invoice->getBaseTerravivesFeeAmount());
        $extensionAttributes->setTerravivesFeeTaxAmount($invoice->getTerravivesFeeTaxAmount());
        $extensionAttributes->setBaseTerravivesFeeTaxAmount($invoice->getBaseTerravivesFeeTaxAmount());
        $extensionAttributes->setTerravivesFeeDetails($invoice->getTerravivesFeeDetails());

        $invoice->setExtensionAttributes($extensionAttributes);

        return $invoice;
    }

    /**
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceSearchResultInterface $invoiceSearchResult
     * @return InvoiceSearchResultInterface
     */
    public function afterGetList(
        InvoiceRepositoryInterface $subject,
        InvoiceSearchResultInterface $invoiceSearchResult
    ) {
        /** @var InvoiceInterface $invoice */
        foreach ($invoiceSearchResult->getItems() as $invoice) {
            $this->afterGet($subject, $invoice);
        }

        return $invoiceSearchResult;
    }
}
