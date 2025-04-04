<?php

use App\Classes\Order;
use App\Classes\OrderProcessingService;

// Test when user does not have any orders
test('processOrders returns empty array when user has no orders', function () {
    // Arrange
    $dbService = mockDatabaseService([]); // Empty orders array
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    
    // Act
    $result = $service->processOrders(1);
    
    // Assert
    expect($result)->toBe([]);
});

// Test when an exception is thrown during the processing
test('processOrders returns false when an exception is thrown', function () {
    // Arrange
    $dbService = Mockery::mock(\App\Classes\DatabaseService::class);
    $dbService->shouldReceive('getOrdersByUser')
        ->andThrow(new \Exception('General error'));
    
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    
    // Act
    $result = $service->processOrders(1);
    
    // Assert
    expect($result)->toBeFalse();
});

// Test for proper iteration through multiple orders
test('processOrders processes multiple orders correctly', function () {
    // Arrange
    $orders = [
        new Order(1, 'A', 100, false),
        new Order(2, 'B', 80, false),
        new Order(3, 'C', 120, true)
    ];
    
    $dbService = mockDatabaseService($orders);
    $apiClient = mockAPIClient();
    $fileSystem = mockFileSystem();
    
    $service = new OrderProcessingService($dbService, $apiClient, $fileSystem);
    
    // Act
    $result = $service->processOrders(1);
    
    // Assert
    expect($result)->toHaveCount(3);
    expect($result[0]->status)->toBe('exported');
    expect($result[1]->status)->toBe('processed');
    expect($result[2]->status)->toBe('completed');
}); 