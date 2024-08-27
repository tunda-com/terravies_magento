<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model\ResourceModel\Order;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{

    protected $request;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        $this->request = $request;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     */
    public function _beforeLoad()
    {
        $this->getSelect()->joinLeft(
            ['order' => $this->getTable('sales_order')],
            'main_table.' . 'entity_id' . ' = order.' . 'entity_id',
            [
                'base_terravives_fee_amount',
                'base_terravives_fee_refunded',
                'base_terravives_fee_cancelled',
                'terravives_fee_details'
            ]
        );

        $this->addExpressionFieldToSelect(
            'base_terravives_fee_amount',
            'order.base_terravives_fee_amount',
            'order.base_terravives_fee_amount'
        );
        $this->addExpressionFieldToSelect(
            'base_terravives_fee_refunded',
            'order.base_terravives_fee_refunded',
            'order.base_terravives_fee_refunded'
        );
        $this->addExpressionFieldToSelect(
            'base_terravives_fee_cancelled',
            'order.base_terravives_fee_cancelled',
            'order.base_terravives_fee_cancelled'
        );
        $this->addExpressionFieldToSelect(
            'terravives_fee_details',
            'order.terravives_fee_details',
            'order.terravives_fee_details'
        );

        $this->addFieldToFilter('order.terravives_fee_details', ['gt' => '0']);
        $this->addFieldToFilter('order.base_terravives_fee_amount', ['gt' => '0']);

        return parent::_beforeLoad();
    }
}
