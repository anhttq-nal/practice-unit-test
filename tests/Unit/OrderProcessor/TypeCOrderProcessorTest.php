<?php

use App\Classes\Order;
use App\Classes\OrderProcessor\TypeCOrderProcessor;

test('process sets status to completed when flag is true', function () {
    // Arrange
    $processor = new TypeCOrderProcessor();
    $order = new Order(1, 'C', 100, true);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('completed');
});

test('process sets status to in_progress when flag is false', function () {
    // Arrange
    $processor = new TypeCOrderProcessor();
    $order = new Order(1, 'C', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('in_progress');
}); 