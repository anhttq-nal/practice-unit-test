<?php

use App\Classes\Order;
use App\Classes\OrderProcessingService;
use App\Classes\OrderProcessor\OrderProcessorFactory;

// Test private methods by using reflection
function callPrivateMethod($object, $methodName, array $parameters = [])
{
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);
    return $method->invokeArgs($object, $parameters);
}

test('updateOrderPriority sets priority to high when amount > 200', function () {
    // Arrange
    $dbService = mockDatabaseService();
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    $order = new Order(1, 'A', 201, false);
    
    // Act - Call the private method
    callPrivateMethod($service, 'updateOrderPriority', [$order]);
    
    // Assert
    expect($order->priority)->toBe('high');
});

test('updateOrderPriority with exact boundary amount 200 sets priority to low', function () {
    // Arrange
    $dbService = mockDatabaseService();
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    $order = new Order(1, 'A', 200, false);
    
    // Act - Call the private method
    callPrivateMethod($service, 'updateOrderPriority', [$order]);
    
    // Assert
    expect($order->priority)->toBe('low');
});

test('updateOrderPriority sets priority to low when amount < 200', function () {
    // Arrange
    $dbService = mockDatabaseService();
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    $order = new Order(1, 'A', 199, false);
    
    // Act - Call the private method
    callPrivateMethod($service, 'updateOrderPriority', [$order]);
    
    // Assert
    expect($order->priority)->toBe('low');
}); 