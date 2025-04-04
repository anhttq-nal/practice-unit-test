<?php

use App\Classes\Order;
use App\Classes\OrderProcessor\TypeAOrderProcessor;
use App\Classes\OrderProcessor\TypeBOrderProcessor;

// Test boundary condition for high value order note (around 150)
test('TypeA edge case: exact boundary value amount = 150 does not trigger high value note', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(true);
    
    $writeCallParams = [];
    $fileSystem->shouldReceive('writeCSV')
        ->andReturnUsing(function($fileHandle, $data) use (&$writeCallParams) {
            $writeCallParams[] = $data;
            return null;
        });
    
    $fileSystem->shouldReceive('closeFile');
    
    $processor = new TypeAOrderProcessor($fileSystem);
    $order = new Order(1, 'A', 150, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect(count($writeCallParams))->toBe(2); // Only header and data row
});

test('TypeA edge case: amount just above boundary (151) adds high value note', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(true);
    
    $writeCallParams = [];
    $fileSystem->shouldReceive('writeCSV')
        ->andReturnUsing(function($fileHandle, $data) use (&$writeCallParams) {
            $writeCallParams[] = $data;
            return null;
        });
    
    $fileSystem->shouldReceive('closeFile');
    
    $processor = new TypeAOrderProcessor($fileSystem);
    $order = new Order(1, 'A', 151, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect(count($writeCallParams))->toBe(3); // Header, data row, and note
    expect($writeCallParams[2][5])->toBe('High value order');
});

// Test boundary condition for order priority (around 200)
test('Boundary test: amount exactly at 200 sets priority to low', function () {
    // Arrange
    $order = new Order(1, 'A', 200, false);
    
    // Get a reference to the private method
    $reflection = new \ReflectionClass(\App\Classes\OrderProcessingService::class);
    $method = $reflection->getMethod('updateOrderPriority');
    $method->setAccessible(true);
    
    // Create service instance
    $service = new \App\Classes\OrderProcessingService(
        mockDatabaseService(),
        mockAPIClient(),
        mockFileSystem()
    );
    
    // Act
    $method->invokeArgs($service, [$order]);
    
    // Assert
    expect($order->priority)->toBe('low');
});

test('Boundary test: amount just over threshold (201) sets priority to high', function () {
    // Arrange
    $order = new Order(1, 'A', 201, false);
    
    // Get a reference to the private method
    $reflection = new \ReflectionClass(\App\Classes\OrderProcessingService::class);
    $method = $reflection->getMethod('updateOrderPriority');
    $method->setAccessible(true);
    
    // Create service instance
    $service = new \App\Classes\OrderProcessingService(
        mockDatabaseService(),
        mockAPIClient(),
        mockFileSystem()
    );
    
    // Act
    $method->invokeArgs($service, [$order]);
    
    // Assert
    expect($order->priority)->toBe('high');
});

// Test boundary condition for TypeB processor (around 50 for data, around 100 for amount)
test('TypeB edge case: boundary data value 49 with amount < 100', function () {
    // Arrange
    $apiClient = Mockery::mock(\App\Classes\APIClient::class);
    $apiResponse = Mockery::mock(\App\Classes\APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 49;
    
    $apiClient->shouldReceive('callAPI')->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 99, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('pending');
});

test('TypeB edge case: exact data boundary 50 with amount < 100', function () {
    // Arrange
    $apiClient = Mockery::mock(\App\Classes\APIClient::class);
    $apiResponse = Mockery::mock(\App\Classes\APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 50;
    
    $apiClient->shouldReceive('callAPI')->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 99, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('processed');
});

test('TypeB edge case: data value 50 with exact amount boundary 100', function () {
    // Arrange
    $apiClient = Mockery::mock(\App\Classes\APIClient::class);
    $apiResponse = Mockery::mock(\App\Classes\APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 50;
    
    $apiClient->shouldReceive('callAPI')->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('error');
}); 