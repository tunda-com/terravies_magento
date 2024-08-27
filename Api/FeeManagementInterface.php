<?php
/**
 *
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Api;

use Terravives\Fee\Api\Data\FeeDataInterface;

interface FeeManagementInterface
{
    /**
     * @param int $cartId
     * @param \Terravives\Fee\Api\Data\FeeDataInterface $feeData
     * @return bool
     */
    public function addToCart(int $cartId, FeeDataInterface $feeData): bool;
}
