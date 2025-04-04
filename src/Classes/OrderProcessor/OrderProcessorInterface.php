<?php

namespace App\Classes\OrderProcessor;

use App\Classes\Order;

interface OrderProcessorInterface
{
    public function process(Order $order, int $userId): void;
} 