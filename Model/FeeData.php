<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Model;

use Terravives\Fee\Api\Data\FeeDataInterface;

class FeeData implements FeeDataInterface
{

    /**
     * @var float|null
     */
    protected $amount;

    /**
     * @param float $amount
     * @return FeeDataInterface
     */
    public function setAmount(float $amount): FeeDataInterface
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
