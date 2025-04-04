<?php

namespace App\Classes\OrderProcessor;

use App\Classes\Order;

class TypeCOrderProcessor implements OrderProcessorInterface
{
    public function process(Order $order, int $userId): void
    {
        if ($order->flag) {
            $order->status = 'completed';
        } else {
            $order->status = 'in_progress';
        }
    }
} 