<?php
/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Terravives\Fee\Observer;

use Magento\Framework\Serialize\SerializerInterface;

class ProcessOrder implements  \Magento\Framework\Event\ObserverInterface
{
	/**
     * @var \Terravives\Fee\Helper\ApiHelper
     */
    protected $apiHelper;

    /**
     * @var \Terravives\Fee\Logger\Logger
     */
    protected $_logger;
    
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * ProcessOrder constructor.
     *
     * @param Fee $fee
     */
    public function __construct(
    	\Terravives\Fee\Helper\ApiHelper $apiHelper,
        \Terravives\Fee\Logger\Logger $logger,
        SerializerInterface $serializer
    )
    {
        $this->apiHelper = $apiHelper;
        $this->_logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getOrder();
        $orderData= [];
        $feeDetails = $order->getTerravivesFeeDetails();
         
        try {
            if ($feeDetails) {
                $feeDetails = $this->serializer->unserialize($feeDetails);
                $orderData = [
                    "cart_uuid" => $feeDetails['cart_uuid'],
                    "amount" => $feeDetails['fee'],
                    "customer_name" => $order->getCustomerName(),
                    "customer_email" => $order->getCustomerEmail(),
                ];
                $hash = " ";
                foreach ($feeDetails['compensations'] as $key => $value) {
                    if ($value['amount'] == $feeDetails['fee'] || (is_null($value['amount']) && $feeDetails['fee'] > 0))
                    {
                        $hash = $value['hash'];
                    }
                }
                $orderData['hash'] = $hash;


                $resultCreateCompensationOrder = $this->apiHelper->createCompensationOrder( $orderData);
                $orderCheckOut = [
                    "order_uuid" => $resultCreateCompensationOrder['order_uuid'],
                ];

                $resultCreateCompensationOrder = $this->apiHelper->orderCheckout( $orderCheckOut);
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
