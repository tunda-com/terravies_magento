<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderAdmin implements ObserverInterface
{
    /**
     * @var \Terravives\Fee\Helper\Fee
     */
    protected $feeHelper;

    /**
     * AddFeeToOrder constructor.
     *
     * @param \Terravives\Fee\Helper\Fee $feeHelper
     */
    public function __construct(
        \Terravives\Fee\Helper\Fee $feeHelper
    ) {
        $this->feeHelper = $feeHelper;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        // check submit fee when admin/sales_order_create
        $post = $observer->getEvent()->getRequest();

        if (!empty($post['fee'])) {
            $fee      = $post['fee'];
            $modelFee = $this->feeHelper->getNewFeeObject();
            $modelFee->addFeeToQuote($fee);
            $modelFee->getCurrentSession()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
        } elseif (isset($post['delete_fee']) && $post['delete_fee']) {
            $modelFee = $this->feeHelper->getNewFeeObject();
            $modelFee->deleteFeeFromQuote();
            $modelFee->getCurrentSession()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
        }

        return $this;
    }
}
