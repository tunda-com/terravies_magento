<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Block\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\View\Element\Template\Context;
use Terravives\Fee\Helper\Data;
use Terravives\Fee\Helper\Price;
use Terravives\Fee\Model\Fee;

class FeeData extends \Magento\Payment\Block\Form
{
    /**
     * @var \Terravives\Fee\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Terravives\Fee\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Terravives\Fee\Helper\Fee
     */
    protected $helperFee;

    /**
     * @var \Terravives\Fee\Model\Fee
     */
    protected $modelFee;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @param Context $context
     * @param Fee $modelFee
     * @param Data $helperData
     * @param \Terravives\Fee\Helper\Fee $helperFee
     * @param Session $checkoutSession
     * @param Price $helperPrice
     * @param \Magento\Customer\Model\Session $customerSession
     * @param SessionManager $sessionManager
     * @param LocaleFormat $localeFormat
     * @param Json $jsonSerializer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Terravives\Fee\Model\Fee               $modelFee,
        \Terravives\Fee\Helper\Data                  $helperData,
        \Terravives\Fee\Helper\Fee              $helperFee,
        \Magento\Checkout\Model\Session                  $checkoutSession,
        \Terravives\Fee\Helper\Price                 $helperPrice,
        \Magento\Customer\Model\Session                  $customerSession,
        \Magento\Framework\Session\SessionManager        $sessionManager,
        LocaleFormat                                     $localeFormat,
        \Magento\Framework\Serialize\Serializer\Json     $jsonSerializer,
        array                                            $data = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->helperPrice     = $helperPrice;
        $this->modelFee   = $modelFee;
        $this->helperData      = $helperData;
        $this->sessionManager  = $sessionManager;
        $this->helperFee  = $helperFee;
        $this->localeFormat    = $localeFormat;
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function serializeJson($data): string
    {
        return $this->jsonSerializer->serialize($data) ?? '';
    }

    /**
     * Get Store
     *
     * @param null $storeId
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Get fee data
     *
     * @return array
     */
    public function getFeeData()
    {
        $result          = [];
        $feeDetails = $this->modelFee->getQuoteDetailsFee();

        $storeId  = $this->_storeManager->getStore()->getId();
        $result['is_enable'] = $this->helperData->showFeesTab($storeId);
        customlog( isset($feeDetails['compensations']));
        
        if (!isset($feeDetails['compensations'])) {
            $result['is_enable'] = false;
        }

        if (isset($feeDetails['fee'])) {
            $result['fee']        = $this->helperPrice->convertToCurrentCurrency($feeDetails['fee']);
            $result['is_fee_use']         = ($result['fee'] > 0);
        }

        $result['url']                       = $this->getUrl('fees/checkout/fee');
        $result['is_display_title']          = ($result['is_enable'] == false) ? false
            : $this->getIsDisplayTitle();
        $result['is_enable_fees']            = $result['is_enable'];
        $result['default_description_fee']   = $this->helperData->getDefaultDescription($storeId);
        $result['price_format']              = $this->localeFormat->getPriceFormat(
            null,
            $this->helperFee->getQuote()->getQuoteCurrencyCode()
        );

        customlog($result);
        return $result;
    }

    /**
     * Check if display title
     * On the cart page we use the external title wrapper.
     *
     * @return boolean
     */
    public function getIsDisplayTitle()
    {
        $actionList = [];
        if (!empty($this->_data['cart_full_actions']) && is_array($this->_data['cart_full_actions'])) {
            $actionList = $this->_data['cart_full_actions'];
        }
        $actionList[] = 'checkout_cart_index';

        return !in_array($this->_request->getFullActionName(), $actionList);
    }
}
