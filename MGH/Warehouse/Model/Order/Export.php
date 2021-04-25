<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MGH\Warehouse\Model\Order;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
use MGH\Warehouse\Service\WarehouseApiService;

class Export
{

    /**
     * @var WarehouseApiService
     */
    protected WarehouseApiService $warehouseApi;


    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param WarehouseApiService $warehouseApi
     * @param OrderRepositoryInterface $orderRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function _construct(
        WarehouseApiService $warehouseApi,
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->warehouseApi    = $warehouseApi;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig     = $scopeConfig;
    }

    /**
     * @param Order $order
     * @throws \Exception
     */
    public function exportOrderToWarehouse(Order $order)
    {

        // double check if order not already exported, and is in valid status
        if (!$order->getLogisticId() && $this->isValidStatusForExport($order->getStatus())) {
            $response = $this->warehouseApi->execute($this->buildData($order));
            if (!empty($response['logistic_id'])) {
                $order->setLogisticId($response['logistic_id']);

                // we should use order repository for save
                $this->orderRepository->save($order);
            } else {
                // we should receive a logistic ID else we suppose that an error has occurred
                throw new \Exception('No logistic_id found in repsonse');
            }
        }
    }

    /**
     * @param $status
     * @return bool
     */
    public function isValidStatusForExport($status)
    {
        $exportStatuses = $this->scopeConfig->getValue('warehouse/export/order_export_statuses');
        return in_array($status, $exportStatuses);
    }

    /**
     * @param Order $order
     * @return array
     */
    protected function buildData(Order $order)
    {
        $data['customer'] = [
            'email'     => $order->getCustomerEmail() ?? '',
            'firstname' => $order->getCustomerFirstname() ?? 'guest',
            'lastname'  => $order->getCustomerLastname() ?? 'guest',
        ];

        $data['order'] = [
            'shipping_method'  => '',
            'shipping_address' => $this->buildShipping($order->getShippingAddress()),
            'total'            => $order->getBaseGrandTotal(),
            'currency'         => $order->getBaseCurrency(),
            'items'            => $this->buildItems($order->getAllItems()),
            ''                 => '',
        ];

        return $data;
    }

    /**
     * @param Address $address
     * @return array
     */
    protected function buildShipping(Address $address)
    {
        return [
            'street'     => $address->getStreet(),
            'postcode,'  => $address->getPostcode(),
            'city,'      => $address->getCity(),
            'country,'   => $address->getCountryId(),
            'telephone,' => $address->getTelephone(),
        ];
    }

    /**
     * @param Item[] $items
     * @return array
     */
    protected function buildItems(array $items)
    {
        $itemList = [];
        foreach ($items as $item) {
            $itemList[] = [
                'name'  => $item->getName(),
                'sku'   => $item->getSku(),
                'qty'   => $item->getQty(), // May be we need quantity also
                'price' => $items->getPrice(),
            ];
        }
        return $itemList;
    }

}
