<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Controller\Cart;

class Fee extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Fee factory
     *
     * @var \Terravives\Fee\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * @var \Terravives\Fee\Helper\Fee
     */
    protected $helperFee;

    /**
     * fee constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Terravives\Fee\Model\FeeFactory $feeFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Terravives\Fee\Helper\Fee $helperFee
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Terravives\Fee\Model\FeeFactory $feeFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Terravives\Fee\Helper\Fee $helperFee
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->helperFee  = $helperFee;
        $this->feeFactory = $feeFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Initialize fee
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->getRequest()->getParams();
        $feePost = $this->getRequest()->getPost('fee');
    }
}
