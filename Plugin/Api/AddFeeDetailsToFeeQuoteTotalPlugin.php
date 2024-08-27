<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Plugin\Api;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalSegmentInterface;
use Magento\Quote\Model\Cart\TotalsConverter as CartTotalsConverter;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;

class AddFeeDetailsToFeeQuoteTotalPlugin
{
    /**
     * @var TotalSegmentExtensionFactory
     */
    protected $totalSegmentExtensionFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $code;

    /**
     * @param TotalSegmentExtensionFactory $totalSegmentExtensionFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        TotalSegmentExtensionFactory $totalSegmentExtensionFactory,
        SerializerInterface $serializer
    ) {
        $this->totalSegmentExtensionFactory = $totalSegmentExtensionFactory;
        $this->serializer                   = $serializer;
        $this->code                         = 'terravives_fee';
    }

    /**
     * Add terravives_fee segment to the summary
     *
     * @param CartTotalsConverter $subject
     * @param TotalSegmentInterface[] $result
     * @param AddressTotal[] $addressTotals
     * @return TotalSegmentInterface[]
     */
    public function afterProcess(CartTotalsConverter $subject, $result, $addressTotals)
    {
        if (!isset($addressTotals[$this->code])) {
            return $result;
        }

        $feeDetails = $addressTotals[$this->code]->getTerravivesFeeDetails();

        /** @var TotalSegmentExtensionInterface $totalSegmentExtension */
        $totalSegmentExtension = $this->totalSegmentExtensionFactory->create();
        $totalSegmentExtension->setTerravivesFeeDetails($this->serializer->serialize($feeDetails));
        $result[$this->code]->setExtensionAttributes($totalSegmentExtension);

        return $result;
    }
}
