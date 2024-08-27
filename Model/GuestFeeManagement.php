<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;
use Terravives\Fee\Api\Data\FeeDataInterface;
use Terravives\Fee\Api\FeeManagementInterface;

class GuestFeeManagement implements \Terravives\Fee\Api\GuestFeeManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var FeeManagementInterface
     */
    protected $feeManagement;

    /**
     * GuestFeeManagement constructor.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param FeeManagementInterface $feeManagement
     */
    public function __construct(QuoteIdMaskFactory $quoteIdMaskFactory, FeeManagementInterface $feeManagement)
    {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->feeManagement = $feeManagement;
    }

    /**
     * @param string $cartId
     * @param FeeDataInterface $feeData
     * @return bool
     */
    public function addToCart(string $cartId, FeeDataInterface $feeData): bool
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->feeManagement->addToCart((int)$quoteIdMask->getQuoteId(), $feeData);
    }
}
