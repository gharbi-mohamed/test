<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MGH\Warehouse\Console\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Setup\Exception;
use MGH\Warehouse\Model\Order\Export;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WarehouseExportOrders extends Command
{

    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var Export
     */
    protected Export $warehouseExporter;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    public function _construct(
        CollectionFactory $orderCollectionFactory,
        Export $warehouseExporter,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->warehouseExporter      = $warehouseExporter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->scopeConfig            = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $orderLimit          = $input->getArgument('order_limit');
        $orderExportStatuses = $this->$this->scopeConfig->getValue('warehouse/export/order_export_statuses');

        $orders = $this->orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            // apply filters on orders
            ->addFieldToFilter('status', ['in' => $orderExportStatuses])
            ->addFieldToFilter('logistic_id', ['null' => true])
            // apply limitations and pagination for performance purpose
            ->limit($orderLimit);

        foreach ($orders as $order) {
            try {
                $this->warehouseExporter->exportOrderToWarehouse($order);
                // Add log of exported items successfully
            } catch (\Exception $e) {
                // Add log of errors
            }
        }

        $output->writeln("Export finished ");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("mgh_warehouse:warehouseexportorders");
        $this->setDescription("Export orders asynchroniously to warehouse");
        $this->setDefinition([
            new InputArgument('order_limit', InputArgument::OPTIONAL, "Order Limit"),
        ]);
        parent::configure();
    }
}

