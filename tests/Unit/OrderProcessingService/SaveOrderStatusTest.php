<?php

use App\Classes\Order;
use App\Classes\OrderProcessingService;
use App\Classes\DatabaseException;

// Import the helper function
if (!function_exists('callPrivateMethod')) {
    function callPrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}

// Test successful order status update
test('saveOrderStatus successfully updates order status', function () {
    // Arrange
    $order = new Order(1, 'A', 100, false);
    $order->status = 'processed';
    $order->priority = 'high';
    
    // Create mock directly instead of using helper function
    $dbService = Mockery::mock(\App\Classes\DatabaseService::class);
    $dbService->shouldReceive('updateOrderStatus')
        ->once()
        ->with(1, 'processed', 'high')
        ->andReturn(true);
    
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    
    // Act
    callPrivateMethod($service, 'saveOrderStatus', [$order]);
    
    // Assert
    // The test passes if no exceptions are thrown and the mock expectations are met
    expect($order->status)->toBe('processed');
});

// Test when DatabaseException is thrown
test('saveOrderStatus sets status to db_error when DatabaseException is thrown', function () {
    // Arrange
    $order = new Order(1, 'A', 100, false);
    $order->status = 'processed';
    $order->priority = 'high';
    
    $dbService = Mockery::mock(\App\Classes\DatabaseService::class);
    $dbService->shouldReceive('updateOrderStatus')
        ->once()
        ->with(1, 'processed', 'high')
        ->andThrow(new DatabaseException('Database error'));
    
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    
    // Act
    callPrivateMethod($service, 'saveOrderStatus', [$order]);
    
    // Assert
    expect($order->status)->toBe('db_error');
}); 