<?php

namespace App\Classes;

use App\Classes\OrderProcessor\OrderProcessorFactory;

class OrderProcessingService
{
    private $dbService;
    private $apiClient;
    private $fileSystem;
    private $processorFactory;

    public function __construct(
        DatabaseService $dbService, 
        APIClient $apiClient,
        FileSystemInterface $fileSystem = null
    ) {
        $this->dbService = $dbService;
        $this->apiClient = $apiClient;
        $this->fileSystem = $fileSystem ?? new DefaultFileSystem();
        $this->processorFactory = new OrderProcessorFactory($this->fileSystem, $this->apiClient);
    }

    public function processOrders(int $userId)
    {
        try {
            $orders = $this->dbService->getOrdersByUser($userId);

            foreach ($orders as $order) {
                $this->processOrder($order, $userId);
                $this->updateOrderPriority($order);
                $this->saveOrderStatus($order);
            }
            return $orders;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function processOrder(Order $order, int $userId): void
    {
        $processor = $this->processorFactory->createProcessor($order);
        $processor->process($order, $userId);
    }

    private function updateOrderPriority(Order $order): void
    {
        if ($order->amount > 200) {
            $order->priority = 'high';
        } else {
            $order->priority = 'low';
        }
    }

    private function saveOrderStatus(Order $order): void
    {
        try {
            $this->dbService->updateOrderStatus($order->id, $order->status, $order->priority);
        } catch (DatabaseException $e) {
            $order->status = 'db_error';
        }
    }
} 