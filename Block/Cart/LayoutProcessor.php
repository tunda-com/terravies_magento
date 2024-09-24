<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Block\Cart;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Framework\Escaper;
use Psr\Log\LoggerInterface;
use Terravives\Fee\Helper\Fee as HelperFee;
use Terravives\Fee\Helper\Data as HelperData;
use Terravives\Fee\Helper\Price as HelperPrice;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Terravives\Fee\Helper\PredefinedFee as HelperPredefined;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var HelperFee
     */
    protected $helperFee;

    /**
     * @var HelperData
     */
    protected $helperData;
    /**
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * @var ApiHelper
     */
    protected $apihelper;

    /**
     * @var FeeHelper
     */
    protected $feeHelper;

    /**
     * @var  \Terravives\Fee\Model\Fee
     */
    protected $feeModel;

    /**
     * @var  \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var  \Magento\Checkout\Model\Cart
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $storeManager;

    /**
    * @var \Magento\Catalog\Model\ProductRepository
    */
    private $_productRepository;

    /**
    * @var \Magento\Quote\Api\CartRepositoryInterface
    */
    private $_categoryCollectionFactory;

    /**
     * LayoutProcessor constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param AttributeMerger $merger
     * @param Escaper $escaper
     * @param LoggerInterface $logger
     * @param HelperFee $helperFee
     * @param HelperData $helperData
     * @param HelperPrice $helperPrice
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Terravives\Fee\Helper\ApiHelper $apihelper,
        \Terravives\Fee\Helper\Fee $feeHelper,
        \Terravives\Fee\Model\Fee $feeModel,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        AttributeMerger $merger,
        Escaper $escaper,
        LoggerInterface $logger,
        HelperFee $helperFee,
        HelperData $helperData,
        HelperPrice $helperPrice
    ) {
        $this->storeManager             = $storeManager;
        $this->apihelper                = $apihelper;
        $this->feeHelper                = $feeHelper;
        $this->feeModel                 = $feeModel;
        $this->cart                     = $cart;
        $this->quoteRepository          = $quoteRepository;
        $this->_productRepository       = $productRepository;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->merger                   = $merger;
        $this->escaper                  = $escaper;
        $this->logger                   = $logger;
        $this->helperFee                = $helperFee;
        $this->helperData               = $helperData;
        $this->helperPrice              = $helperPrice;
    }

    /**
     * Add components on frontend
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        if (isset(
            $jsLayout['components']['terravives-fee-form-container']['children']
            ['terravives-fee-form-fieldset']['children']
        )
        ) {
            $fieldSetPointer = &$jsLayout['components']['terravives-fee-form-container']['children']
            ['terravives-fee-form-fieldset']['children'];

            foreach ($this->getFormComponents() as $component) {
                $fieldSetPointer[] = $component;
            }
        }


        if (isset(
            $jsLayout['components']['checkout']['children']['sidebar']['children']
            ['summary']['children']['itemsBefore']['children']
            ['terravives-fee-form-container']['children']['terravives-fee-form-fieldset']['children']
        )
        ) {
            $fieldSetPointer = &$jsLayout['components']['checkout']['children']['sidebar']['children']
            ['summary']['children']['itemsBefore']['children']['terravives-fee-form-container']
            ['children']['terravives-fee-form-fieldset']['children'];

            foreach ($this->getFormComponents() as $component) {
                $fieldSetPointer[] = $component;
            }
        }

        return $jsLayout;
    }

    /**
     * Get form components
     *
     * @return array
     */
    public function getFormComponents()
    {

        $components = [];

        if ($this->helperData->showFeesTab()) {

            $optionFromApi = $this->getCompensations();

            if ($optionFromApi && isset($optionFromApi['compensations'])) {
                $defaultOption = $this->helperData->getDefaultOption();
                $components[] = $this->getPredefinedFeeSelectComponent($optionFromApi, $defaultOption );
                $components[] = $this->getInputComponent();
            }
        }

        return $components;
    }

    /**
     * Get predefine fee Component
     *
     * @return array
     */
    protected function getPredefinedFeeSelectComponent($optionFromApi, $defaultOption = false)
    {
        $component               = [];
        $component['component']  = 'Terravives_Fee/js/form/element/select';
        $component['config']     = [
            'customScope' => 'terravivesFeeForm',
            'template'    => 'Terravives_Fee/form/predefined-select',
            'elementTmpl' => 'ui/form/element/select'
        ];
        $component['dataScope']  = 'predefinedFee';
        $component['provider']   = 'checkoutProvider';
        $component['visible']    = true;
        $component['validation'] = [];
        $component['sortOrder']  = 5;

        $options = [];

        $sortedCompensations = [];
        if (is_array($optionFromApi['compensations'])) {
            $count = count($optionFromApi['compensations']);

            foreach ($optionFromApi['compensations'] as $key =>  $value) {
                if (is_null($value['amount'])) {
                    $value['amount'] = 'custom_fee';
                }
                if ($value['amount'] != 0  && $value['amount'] != 'custom_fee') {
                    $sortedCompensations[$key] = $value['amount'];
                }

                $options[$key] =
                    [
                        'label' => $value['title'],
                        'value' => $value['amount'],
                        'hash' => $value['hash'],
                    ];
            }
        }

        $sort = false;
        if($defaultOption) {
            if ($defaultOption == 'min') {
                asort($sortedCompensations);
                $sort = true;
            } elseif ($defaultOption == 'max') {
                arsort($sortedCompensations);
                $sort = true;
            }
        }

        if ($sort) {
            $sortedOptions = [];
            foreach($sortedCompensations as $key => $value) {
                $sortedOptions[$key] = $options[$key];
                unset($options[$key]);
            }
            $options = array_merge($sortedOptions, $options);
        }

        $component['options'] = $options;

        return $component;
    }

    public function getCompensations()
    {
        $optionFromApi = [];

        $fee = $this->feeHelper->getFee();
        $quote = $this->feeHelper->getQuote();
        if ($fee && isset($fee['last_total']) && isset($fee['compensations']) && $fee['last_total'] == $quote->getSubtotal()) {
            return $fee;
        }

        $this->feeModel->deleteFeeFromQuote();

        $dataLoadOptions = [];
        $dataLoadOptions['total_amount'] = $quote->getGrandTotal();

        if ($this->helperData->shouldAddProductData()) {
            $dataLoadOptions['cart'] = [];
            foreach ($quote->getAllVisibleItems() as $item ) {
                $itemData = [
                                "product_id"    => $item->getProduct()->getId(),
                                "name"          => $item->getProduct()->getName(),
                                "price"         => $item->getPrice(),
                                "amount"        => $item->getQty(),
                                "product_category" => null
                            ];

                if ($this->helperData->shouldAddProductCategory()) {
                    $categories = $this->getProductCategories($item->getProduct()->getId());
                    $itemData["product_category"] = $categories;
                }
                $dataLoadOptions['cart'][] = $itemData;
            }
        }

        $optionFromApi = $this->apihelper->loadOptions($dataLoadOptions);

        if ($optionFromApi) {
            $optionFromApi['last_total'] = $quote->getSubtotal();

            $this->feeModel->addFeeToQuote($optionFromApi);
            $cartQuote = $this->cart->getQuote();
            $cartQuote->collectTotals();
            $this->quoteRepository->save($cartQuote);
        }

        return $optionFromApi;
    }

    public function getProductCategories($productId)
    {
        $categoriesString = "";

        try {
            $product =  $this->_productRepository->getById($productId);
            $categoryIds = $product->getCategoryIds();

            $categories = $this->_categoryCollectionFactory->create()
                                                           ->addAttributeToSelect('*')
                                                           ->addIsActiveFilter()
                                                           ->addFieldToFilter('level', ['gt' => 1])
                                                           ->addAttributeToFilter('entity_id', $categoryIds);
            if (count($categories) > 0) {
                foreach ($categories as $category) {
                     $categoriesString .= $category->getName() . ",";
                }
                $categoriesString = rtrim($categoriesString, ',');
            }

            return $categoriesString;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param double $value
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function convertFeeValueToBaseCurrency($value)
    {
        return $this->helperPrice->convertToBaseCurrency($value);
    }

    /**
     * Get Input Component
     *
     * @return array
     */
    protected function getInputComponent()
    {
        $feeData = $this->helperFee->getFee();

        $component              = [];
        $component['component'] = 'Magento_Ui/js/form/element/abstract';
        $component['config']    = [
            'customScope' => 'terravivesFeeForm',
            'template'    => 'Terravives_Fee/form/input',
            'elementTmpl' => 'ui/form/element/input',
        ];

        $component['dataScope'] = 'terravivesFeeForm';
        $component['provider']  = 'checkoutProvider';
        $component['visible']   = false;

        $component['validation']  = [];
        $component['sortOrder']   = 10;
        $component['placeholder'] = $this->helperData->getAmountPlaceholder();

        if (!empty($feeData['fee'])) {
            $component['value'] = $this->escaper->escapeHtml($feeData['fee']);
        }

        return $component;
    }

    /**
     * Get Input Component
     *
     * @return array
     */
    protected function getHiddenInputsComponent($optionFromApi)
    {
        $component              = [];
        $component['component'] = 'Magento_Ui/js/form/element/abstract';
        $component['config']    = [
            'customScope' => 'terravivesFeeForm',
            'template'    => 'Terravives_Fee/form/input',
            'elementTmpl' => 'ui/form/element/input',
        ];

        $component['dataScope'] = 'cart_uuid';
        $component['provider']  = 'checkoutProvider';

        $component['visible'] = false;

        $component['validation']  = [];
        $component['sortOrder']   = 10;

        if ($optionFromApi['cart_uuid']) {
            $component['value'] = $optionFromApi['cart_uuid'];
        }

        return $component;
    }
}
