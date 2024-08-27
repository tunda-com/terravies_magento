<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Block\Cart;

use Magento\Checkout\Model\CompositeConfigProvider;
use Magento\Customer\Model\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;
use Terravives\Fee\Helper\Data;

class Fee extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Terravives\Fee\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Checkout\Model\CompositeConfigProvider
     */
    protected $configProvider;

    /**
     * @var array|\Magento\Checkout\Block\Checkout\LayoutProcessorInterface[]
     */
    protected $layoutProcessors;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CompositeConfigProvider $configProvider
     * @param Data $helperData
     * @param Json $jsonSerializer
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        \Terravives\Fee\Helper\Data $helperData,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->configProvider   = $configProvider;
        $this->layoutProcessors = $layoutProcessors;
        $this->helperData       = $helperData;
        $this->jsonSerializer   = $jsonSerializer;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
    }

    /**
     * Show fees in Cart
     *
     * @return bool
     */
    public function showFeesTab()
    {
        return $this->helperData->showFeesTab();
    }

    /**
     * Retrieve checkout configuration
     *
     * @return array
     */
    public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return $this->jsonSerializer->serialize($this->jsLayout);
    }

    /**
     * Get base url for block.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
