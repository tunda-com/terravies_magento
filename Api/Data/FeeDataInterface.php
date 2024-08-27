<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Api\Data;

interface FeeDataInterface
{
    /**
     * @param float $amount
     * @return FeeDataInterface
     */
    public function setAmount(float $amount): FeeDataInterface;

    /**
     * @return float|null
     */
    public function getAmount();
}
