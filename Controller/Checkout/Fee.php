<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Controller\Checkout;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Terravives\Fee\Helper\Data;
use Terravives\Fee\Helper\Price;

class Fee extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Terravives\Fee\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Terravives\Fee\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \Terravives\Fee\Model\Fee
     */
    protected $modelFee;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param Cart $cart
     * @param CartRepositoryInterface $quoteRepository
     * @param Data $helperData
     * @param Price $helperPrice
     * @param \Terravives\Fee\Model\Fee $modelFee
     * @param PriceCurrencyInterface $priceCurrency
     * @param Json $jsonSerializer
     * @param Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Terravives\Fee\Helper\Data $helperData,
        \Terravives\Fee\Helper\Price $helperPrice,
        \Terravives\Fee\Model\Fee $modelFee,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->quoteRepository = $quoteRepository;
        $this->helperData      = $helperData;
        $this->helperPrice     = $helperPrice;
        $this->modelFee   = $modelFee;
        $this->escaper         = $escaper;
        $this->storeManager    = $storeManager;
        $this->priceCurrency   = $priceCurrency;
        $this->jsonSerializer  = $jsonSerializer;
    }

    /**
     * Processing of fee requests
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        /* if press button deleteFee or unchecked round up*/
        $isDelete = $this->deleteFee();
        if ($isDelete) {
            return $this->getResponse()->setBody($this->jsonSerializer->serialize(['result' => 'true']));
        }

        /* if press button addFee or add round up*/
        $this->addFee($data);
    }

    /**
     * Delete Fee and round up
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function deleteFee(): bool
    {
        $isFeeDelete = $this->getRequest()->getParam('deleteFee');

        /* if press button deleteFee or unchecked Fee Round Up */
        if ($isFeeDelete) {
            $this->modelFee->deleteFeeFromQuote();
            $cartQuote = $this->cart->getQuote();
            $cartQuote->collectTotals();
            $this->quoteRepository->save($cartQuote);

            return true;
        }

        return false;
    }

    /**
     * Add Fee and round up
     *
     * @param array $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addFee(array $data)
    {
        $fee = $this->getFee($data);
        if (is_null($fee)) {
            $message = __('Please specify a fee amount.');
            $this->messageManager->addErrorMessage($message);

            return $this->getResponse()->setBody(
                $this->jsonSerializer->serialize(
                    [
                        'result' => 'false',
                        'error'  => $message
                    ]
                )
            );
        }

        $currentData = $this->modelFee->getQuoteDetailsFee();

        $currentData['fee'] = $fee;
        $this->modelFee->addFeeToQuote($currentData);
        $cartQuote = $this->cart->getQuote();
        $cartQuote->collectTotals();
        $this->quoteRepository->save($cartQuote);

        return $this->getResponse()->setBody($this->jsonSerializer->serialize(['result' => 'true']));
    }

    /**
     * @param array $data
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getFee(array $data)
    {
        if (empty($data['fee'])) {
            return 0;
        }

        return $this->helperPrice->convertToBaseCurrency($data['fee']);
    }
}
