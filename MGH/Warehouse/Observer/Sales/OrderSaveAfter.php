<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MGH\Warehouse\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use MGH\Warehouse\Model\Order\Export;

/**
 * Method 1 to send data to warehouse using async messages
 */
class OrderSaveAfter implements ObserverInterface
{

    /**
     * @var Export
     */
    protected Export $warehouseExporter;

    public function _construct(Export $warehouseExporter)
    {
        $this->warehouseExporter = $warehouseExporter;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(
        Observer $observer
    )
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getData('order');
        $liveMode = 1; // TODO be replaced with config $this->$this->scopeConfig->getValue('warehouse/export/live_notification')
        if ($this->warehouseExporter->isValidStatusForExport($order->getStatus()) && $liveMode) {
            // TODO put this in an async queue (didn't have enough time)
            $this->warehouseExporter->exportOrderToWarehouse($order);
        }
    }

}

