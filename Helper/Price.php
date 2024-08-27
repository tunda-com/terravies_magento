<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Helper;

class Price extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PRECISION = 2;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Price constructor.
     *
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->storeManager  = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Format price
     *
     * @param double $price
     *
     * @return float
     */
    public function getFormatPrice($price)
    {
        $currency    = $this->storeManager->getStore()->getCurrentCurrency();
        $formatPrice = $this->priceCurrency->format($price, false, self::PRECISION, null, $currency);

        return $formatPrice;
    }

    /**
     * @param double $price
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertToBaseCurrency($price)
    {
        $convertedPrice = $this->priceCurrency->convert(
            $price,
            $this->storeManager->getStore(),
            $this->storeManager->getStore()->getCurrentCurrency()
        );

        return ($price*$price)/$convertedPrice;

    }

    /**
     * @param doublr $price
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function convertToCurrentCurrency($price)
    {
        return $this->priceCurrency->convert(
            $price,
            $this->storeManager->getStore(),
            $this->storeManager->getStore()->getCurrentCurrency()
        );
    }
}
