<?php
/**
 * Copyright © Terravives. All rights reserved.
 
 */
declare(strict_types=1);

namespace Terravives\Fee\Block\PayPal\Express;

class Fee extends \Terravives\Fee\Block\Cart\Fee
{
    /**
     * {@inheritdoc}
     */
    public function showFeesTab(): bool
    {
        return true;
    }
}
