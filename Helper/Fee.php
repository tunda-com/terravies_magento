<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Helper\Context;
use Terravives\Fee\Model\FeeFactory;

class Fee extends \Magento\Framework\App\Helper\AbstractHelper
{

    const PRECISION = 2;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Quote
     */
    protected $adminQuoteSession;

    /**
     * @var FeeFactory
     */
    protected $feeFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Fee constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param State $appState
     * @param Quote $adminQuoteSession
     * @param ObjectManagerInterface $objectManager
     * @param FeeFactory $feeFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        State $appState,
        Quote $adminQuoteSession,
        ObjectManagerInterface $objectManager,
        FeeFactory $feeFactory,
        SerializerInterface $serializer
    ) {
        $this->customerSession   = $customerSession;
        $this->checkoutSession   = $checkoutSession;
        $this->appState          = $appState;
        $this->adminQuoteSession = $adminQuoteSession;
        $this->objectManager     = $objectManager;
        $this->feeFactory   = $feeFactory;
        $this->serializer        = $serializer;

        parent::__construct($context);
    }

    /**
     * Return Fee data from session
     *
     * @return array
     */
    public function getFee()
    {
        $fee = $this->feeFactory->create();
        $data     = $fee->getQuoteDetailsFee();

        return $data;
    }

    /**
     * Return fee
     *
     * @return \Terravives\Fee\Model\Fee
     */
    public function getNewFeeObject()
    {
        return $this->feeFactory->create();
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return mixed
     */
    public function getSalesAddress($quote)
    {
        $address = $quote->getShippingAddress();
        if (!$address->getSubtotal()) {
            $address = $quote->getBillingAddress();
        }

        return $address;
    }

    /**
     * Get current checkout quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            $quote = $this->adminQuoteSession->getQuote();
        } else {
            $quote = $this->checkoutSession->getQuote();
        }

        return $quote;
    }

    /**
     * Ger current session
     *
     * @return \Magento\Backend\Model\Session\Quote|\Magento\Checkout\Model\Session
     */
    public function getCurrentSession()
    {
        $areaCode = $this->appState->getAreaCode();
        if ($areaCode == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->objectManager->get(\Magento\Backend\Model\Session\Quote::class);
        }

        return $this->checkoutSession;
    }

}
