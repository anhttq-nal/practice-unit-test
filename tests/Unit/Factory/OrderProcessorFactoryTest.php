<?php

use App\Classes\Order;
use App\Classes\OrderProcessor\OrderProcessorFactory;
use App\Classes\OrderProcessor\TypeAOrderProcessor;
use App\Classes\OrderProcessor\TypeBOrderProcessor;
use App\Classes\OrderProcessor\TypeCOrderProcessor;
use App\Classes\OrderProcessor\UnknownTypeOrderProcessor;

test('createProcessor returns TypeAOrderProcessor for type A order', function () {
    // Arrange
    $fileSystem = mockFileSystem();
    $apiClient = mockAPIClient();
    $factory = new OrderProcessorFactory($fileSystem, $apiClient);
    $order = new Order(1, 'A', 100, false);
    
    // Act
    $processor = $factory->createProcessor($order);
    
    // Assert
    expect($processor)->toBeInstanceOf(TypeAOrderProcessor::class);
});

test('createProcessor returns TypeBOrderProcessor for type B order', function () {
    // Arrange
    $fileSystem = mockFileSystem();
    $apiClient = mockAPIClient();
    $factory = new OrderProcessorFactory($fileSystem, $apiClient);
    $order = new Order(1, 'B', 100, false);
    
    // Act
    $processor = $factory->createProcessor($order);
    
    // Assert
    expect($processor)->toBeInstanceOf(TypeBOrderProcessor::class);
});

test('createProcessor returns TypeCOrderProcessor for type C order', function () {
    // Arrange
    $fileSystem = mockFileSystem();
    $apiClient = mockAPIClient();
    $factory = new OrderProcessorFactory($fileSystem, $apiClient);
    $order = new Order(1, 'C', 100, false);
    
    // Act
    $processor = $factory->createProcessor($order);
    
    // Assert
    expect($processor)->toBeInstanceOf(TypeCOrderProcessor::class);
});

test('createProcessor returns UnknownTypeOrderProcessor for unknown order type', function () {
    // Arrange
    $fileSystem = mockFileSystem();
    $apiClient = mockAPIClient();
    $factory = new OrderProcessorFactory($fileSystem, $apiClient);
    $order = new Order(1, 'X', 100, false);
    
    // Act
    $processor = $factory->createProcessor($order);
    
    // Assert
    expect($processor)->toBeInstanceOf(UnknownTypeOrderProcessor::class);
}); 