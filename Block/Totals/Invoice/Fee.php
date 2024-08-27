<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Block\Totals\Invoice;

use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Serialize\SerializerInterface;

class Fee extends \Magento\Sales\Block\Order\Totals
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Fee constructor.
     *
     * @param SerializerInterface $serializer
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->serializer        = $serializer;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Add Terravives Fee Amount to Invoice
     *
     * @return void
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals $totalsBlock */
        $totalsBlock = $this->getParentBlock();
        $invoice     = $totalsBlock->getInvoice();

        $label = __('Fee');
        if ($invoice->getTerravivesFeeDetails()) {
            $feeDetails = $this->serializer->unserialize($invoice->getTerravivesFeeDetails());
        }

        if ((float)$invoice->getTerravivesFeeAmount()) {
            $data = [
                'code'       => 'terravives_fee_amount',
                'label'      => $label,
                'value'      => $invoice->getTerravivesFeeAmount(),
                'base_value' => $invoice->getBaseTerravivesFeeAmount()
            ];

            /** @var \Magento\Framework\DataObject $dataObject */
            $dataObject = $this->dataObjectFactory->create($data);

            $totalsBlock->addTotalBefore($dataObject, 'grand_total');
        }
    }
}
