<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Terravives\Fee\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface as CartRepository;
use Terravives\Fee\Api\Data\FeeDataInterface;
use Terravives\Fee\Helper\Price as HelperPrice;
use Terravives\Fee\Helper\Data as HelperData;
use Magento\Quote\Model\Quote;
use Magento\Framework\Exception\LocalizedException;

class FeeManagement implements \Terravives\Fee\Api\FeeManagementInterface
{
    /**
     * @var Fee
     */
    protected $modelFee;

    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * FeeManagement constructor.
     *
     * @param Fee $modelFee
     * @param CartRepository $cartRepository
     * @param HelperPrice $helperPrice
     * @param HelperData $helperData
     */
    public function __construct(
        Fee $modelFee,
        CartRepository $cartRepository,
        HelperPrice $helperPrice,
        HelperData $helperData
    ) {
        $this->modelFee  = $modelFee;
        $this->cartRepository = $cartRepository;
        $this->helperPrice    = $helperPrice;
        $this->helperData     = $helperData;
    }

    /**
     * @param int $cartId
     * @param FeeDataInterface $feeData
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addToCart(int $cartId, FeeDataInterface $feeData): bool
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);

        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %cartId doesn\'t contain products', $cartId));
        }

        $amount  = $this->getAmount($feeData, $quote);

        if ($amount > 0) {
            $minFees = $this->helperData->getMinimumFee();

            if ($amount < $minFees) {
                $price = $this->helperPrice->getFormatPrice($minFees);

                throw new InputException(__('Minimum accepted fee is %1', $price));
            }

            $this->modelFee->addFeeToQuote($amount, $quote);
        } else {
            $this->modelFee->deleteFeeFromQuote($quote);
        }

        $quote->collectTotals();
        $this->cartRepository->save($quote);

        return true;
    }

    /**
     * @param FeeDataInterface $feeData
     * @param Quote $quote
     * @return float
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getAmount(FeeDataInterface $feeData, Quote $quote): float
    {
        if ($feeData->getAmount() > 0) {
            return (float)$this->helperPrice->convertToBaseCurrency($feeData->getAmount());
        }

        if ($feeData->getAmount() === null) {
            $feeDetails = $this->modelFee->getQuoteDetailsFee($quote);

            return empty($feeDetails['fee']) ? 0 : (float)$feeDetails['fee'];
        }

        return 0;
    }

}
