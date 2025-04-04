<?php

namespace App\Classes\OrderProcessor;

use App\Classes\APIClient;
use App\Classes\FileSystemInterface;
use App\Classes\Order;

class OrderProcessorFactory
{
    private FileSystemInterface $fileSystem;
    private APIClient $apiClient;

    public function __construct(FileSystemInterface $fileSystem, APIClient $apiClient)
    {
        $this->fileSystem = $fileSystem;
        $this->apiClient = $apiClient;
    }

    public function createProcessor(Order $order): OrderProcessorInterface
    {
        switch ($order->type) {
            case 'A':
                return new TypeAOrderProcessor($this->fileSystem);
            case 'B':
                return new TypeBOrderProcessor($this->apiClient);
            case 'C':
                return new TypeCOrderProcessor();
            default:
                return new UnknownTypeOrderProcessor();
        }
    }
} 