<?php

namespace App\Classes\OrderProcessor;

use App\Classes\Order;

class UnknownTypeOrderProcessor implements OrderProcessorInterface
{
    public function process(Order $order, int $userId): void
    {
        $order->status = 'unknown_type';
    }
} 