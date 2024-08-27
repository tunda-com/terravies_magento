<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model\Fee;

use Terravives\Fee\Api\Data\FeeDetailsInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class FeeDetails extends AbstractSimpleObject implements FeeDetailsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFeeDetails()
    {
        return isset($this->_data['terravives_fee_details']) ? $this->_data['terravives_fee_details'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeeDetails($details)
    {
        return $this->setData('terravives_fee_details', $details);
    }
}
