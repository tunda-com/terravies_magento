<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Terravives\Fee\Helper\PredefinedFee;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config paths to settings
     */
    const ENABLE_FEES                      = 'terravives_fees/main/enable_fees';
    const FEE_TAX_CALCULATION_INCLUDES_TAX = 'terravives_fees/main/tax_calculation_includes_tax';
    const SHOW_FEES_ADMIN                  = 'terravives_fees/main/show_fees_admin';
    const DEFAULT_DESCRIPTION              = 'terravives_fees/main/default_description_fees';
    const AMOUNT_PLACEHOLDER               = 'terravives_fees/main/fees_amount_placeholder';

    const ENABLE_PRODUCT_DATA              = 'terravives_fees/general/add_product_data';
    const ENABLE_PRODUCT_CATEGORY          = 'terravives_fees/general/add_product_categories';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Request\Http $httpRequest
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(

        \Magento\Framework\App\Request\Http $httpRequest,
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {

        $this->httpRequest              = $httpRequest;
        $this->storeManager             = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isEnableFees($storeId = null)
    {
        $config = $this->scopeConfig->isSetFlag(
            self::ENABLE_FEES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $config;
    }


    /**
     * @param null $storeId
     * @return bool
     */
    public function showFeesTab($storeId = null)
    {
        if ($this->isEnableFees($storeId)) {
            return true;
        }

        return false;
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isShowFeeAdmin($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::SHOW_FEES_ADMIN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function shouldAddProductData($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_PRODUCT_DATA,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function shouldAddProductCategory($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_PRODUCT_CATEGORY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    /**
     * Get default description
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getDefaultDescription($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::DEFAULT_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default amount placeholder
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getAmountPlaceholder($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::AMOUNT_PLACEHOLDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function isTaxCalculationIncludesTax($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::FEE_TAX_CALCULATION_INCLUDES_TAX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isPaypalReviewOrderPage(): bool
    {
        return $this->httpRequest->getFullActionName() == 'paypal_express_review';
    }
}
