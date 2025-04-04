<?php

use App\Classes\Order;
use App\Classes\OrderProcessor\UnknownTypeOrderProcessor;

test('process sets status to unknown_type', function () {
    // Arrange
    $processor = new UnknownTypeOrderProcessor();
    $order = new Order(1, 'X', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('unknown_type');
}); 