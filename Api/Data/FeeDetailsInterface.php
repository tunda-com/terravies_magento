<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Api\Data;

interface FeeDetailsInterface
{
    /**
     * @return string
     */
    public function getFeeDetails();

    /**
     * @param string $details
     * @return $this
     */
    public function setFeeDetails($details);
}
