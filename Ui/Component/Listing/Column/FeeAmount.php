<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Terravives\Fee\Helper\Image as Helper;
use Terravives\Fee\Helper\Price as HelperPrice;
use Terravives\Fee\Model\ResourceModel\Sales;
use Magento\Framework\Serialize\SerializerInterface;

class FeeAmount extends Column
{
    /**
     * @var HelperPrice
     */
    protected $helperPrice;

    /**
     * Collected constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param HelperPrice $helperPrice
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        HelperPrice $helperPrice,
        array $components = [],
        array $data = []
    ) {
        $this->helperPrice = $helperPrice;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['base_terravives_fee_amount'] = $this->helperPrice->getFormatPrice(
                    $item['base_terravives_fee_amount'] -
                    $item['base_terravives_fee_refunded'] -
                    $item['base_terravives_fee_cancelled']
                );
            }
        }

        return $dataSource;
    }
}
