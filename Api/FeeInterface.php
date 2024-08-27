<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Api;

use Magento\Quote\Api\Data\CartInterface;

interface FeeInterface
{
    /**
     * @param array $fee
     * @param CartInterface|null $quote
     * @return mixed
     */
    public function addFeeToQuote(
        array $fee,
        CartInterface $quote = null
    );

    /**
     * Delete fee
     *
     */
    public function deleteFeeFromQuote();
}
