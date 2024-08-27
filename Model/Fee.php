<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\CartInterface;
use Terravives\Fee\Api\FeeInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Store\Model\StoreManagerInterface;
use Terravives\Fee\Helper\Fee as HelperFee;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class Fee implements FeeInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HelperFee
     */
    protected $helperFee;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Fee constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param HelperFee $helperFee
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        HelperFee $helperFee,
        SerializerInterface $serializer
    ) {
        $this->checkoutSession   = $checkoutSession;
        $this->storeManager      = $storeManager;
        $this->helperFee    = $helperFee;
        $this->serializer    = $serializer;
    }

    /**
     * @param float $fee
     * @param Quote|null $quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addFeeToQuote(
        array $fee,
        CartInterface $quote = null
    ) {
        $this->setQuoteDetailsFee($fee, $quote);
    }

    /**
     * @param Quote|null $quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteFeeFromQuote(Quote $quote = null)
    {
        $this->cleanQuoteDetailsFee($quote);
    }

    /**
     * Get current session
     *
     * @return \Magento\Backend\Model\Session\Quote|\Magento\Checkout\Model\Session
     */
    public function getCurrentSession()
    {
        return $this->helperFee->getCurrentSession();
    }

    /**
     * @param array $feeQuoteData
     * @param float $fee
     * @return array
     * @throws NoSuchEntityException
     */
    protected function modifyFeeDetailsByPostData(
        array $feeQuoteData,
        float $fee
    ) {
        $feeQuoteData['global_fee']  = 0;
        $feeQuoteData['fee']         = 0;

        if ($fee) {
            $feeQuoteData['fee']        = $fee;
            $feeQuoteData['global_fee'] = $fee;
        }

        return $feeQuoteData;
    }

    /**
     * @param array $feeQuoteData
     * @param Quote|null $quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function setQuoteDetailsFee(array $feeQuoteData, Quote $quote = null)
    {
        if ($quote === null) {
            $quote = $this->getCurrentSession()->getQuote();
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        if ($address) {
            $address->setTerravivesFeeDetails($this->serializer->serialize($feeQuoteData));
        }
    }


    /**
     * @param Quote|null $quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function cleanQuoteDetailsFee(Quote $quote = null)
    {
        if ($quote === null) {
            $quote = $this->getCurrentSession()->getQuote();
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        if ($address) {
            $address->setTerravivesFeeDetails(null);
        }
    }

    /**
     * @param Quote|null $quote
     * @return array|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteDetailsFee(Quote $quote = null)
    {
        if ($quote === null) {
            $quote = $this->getCurrentSession()->getQuote();
        }

        $address = $quote->getIsVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        if ($address && $address->getTerravivesFeeDetails()) {
            return $this->serializer->unserialize($address->getTerravivesFeeDetails());
        }

        return null;
    }
}
