<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Sales extends AbstractDb
{
    const STATUS_PENDING  = 'pending';
    const STATUS_CLOSED   = 'closed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_HOLDED   = 'holded';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * Sales constructor.
     *
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        $this->connection = $context->getResources();
        parent::__construct($context, $connectionName);
    }

    /**
     * Abstract Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_sequence_meta', 'meta_id');
    }

    /**
     * @return array
     */
    public function getFeeData()
    {
        $connect = $this->connection->getConnection();
        $select  = $connect->select()
                           ->from(
                               [$this->getTable('sales_order')],
                               [
                                   'base_terravives_fee_amount',
                                   'base_terravives_fee_refunded',
                                   'base_terravives_fee_cancelled',
                                   'terravives_fee_details'
                               ]
                           )
                           ->where('terravives_fee_details != ?', '')
                           ->where('status != ?', self::STATUS_PENDING)
                           ->where('status != ?', self::STATUS_CLOSED)
                           ->where('status != ?', self::STATUS_CANCELED)
                           ->where('status != ?', self::STATUS_HOLDED);

        return $connect->fetchAll($select);
    }
}
