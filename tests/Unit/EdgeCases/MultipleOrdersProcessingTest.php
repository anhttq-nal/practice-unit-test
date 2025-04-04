<?php

use App\Classes\Order;
use App\Classes\OrderProcessingService;
use App\Classes\APIException;
use App\Classes\DatabaseException;

beforeEach(function () {
    $this->dbService = Mockery::mock(\App\Classes\DatabaseService::class);
    $this->apiClient = Mockery::mock(\App\Classes\APIClient::class);
    $this->fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
});

test('processOrders handles mix of different order types correctly', function () {
    // Arrange - Create orders of different types
    $orders = [
        new Order(1, 'A', 100, false),
        new Order(2, 'B', 90, false),
        new Order(3, 'C', 120, true),
        new Order(4, 'X', 200, false)
    ];
    
    // Mock dependencies behavior for each order type
    $this->dbService->shouldReceive('getOrdersByUser')
        ->once()
        ->andReturn($orders);
    
    // For Type A Order
    $this->fileSystem->shouldReceive('openFile')
        ->once()
        ->andReturn(true);
    $this->fileSystem->shouldReceive('writeCSV')
        ->times(2); // Header and data rows
    $this->fileSystem->shouldReceive('closeFile')
        ->once();
    
    // For Type B Order
    $apiResponse = Mockery::mock(\App\Classes\APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 60;
    
    $this->apiClient->shouldReceive('callAPI')
        ->once()
        ->with(2)
        ->andReturn($apiResponse);
    
    // For all orders - database update
    $this->dbService->shouldReceive('updateOrderStatus')
        ->times(4)
        ->andReturn(true);
    
    $service = new OrderProcessingService($this->dbService, $this->apiClient, $this->fileSystem);
    
    // Act
    $result = $service->processOrders(1);
    
    // Assert
    expect($result)->toHaveCount(4);
    expect($result[0]->status)->toBe('exported');
    expect($result[1]->status)->toBe('processed');
    expect($result[2]->status)->toBe('completed');
    expect($result[3]->status)->toBe('unknown_type');
});

test('processOrders handles when some orders throw exceptions', function () {
    // Arrange - Create orders of different types
    $orders = [
        new Order(1, 'A', 100, false),
        new Order(2, 'B', 90, false),
        new Order(3, 'C', 120, true)
    ];
    
    // Mock dependencies behavior
    $this->dbService->shouldReceive('getOrdersByUser')
        ->once()
        ->andReturn($orders);
    
    // For Type A Order
    $this->fileSystem->shouldReceive('openFile')
        ->once()
        ->andReturn(true);
    $this->fileSystem->shouldReceive('writeCSV')
        ->times(2); // Header and data rows
    $this->fileSystem->shouldReceive('closeFile')
        ->once();
    
    // For Type B Order - throw exception
    $this->apiClient->shouldReceive('callAPI')
        ->once()
        ->with(2)
        ->andThrow(new APIException('API error'));
    
    // Expect order status updates - allow any number of calls
    $this->dbService->shouldReceive('updateOrderStatus')
        ->andReturn(true);
    
    $service = new OrderProcessingService($this->dbService, $this->apiClient, $this->fileSystem);
    
    // Act
    $result = $service->processOrders(1);
    
    // Assert
    expect($result)->toHaveCount(3);
    expect($result[0]->status)->toBe('exported');
    expect($result[1]->status)->toBe('api_failure');
    expect($result[2]->status)->toBe('completed');
}); 