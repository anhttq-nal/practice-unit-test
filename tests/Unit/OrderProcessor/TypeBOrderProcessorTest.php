<?php

use App\Classes\APIClient;
use App\Classes\APIException;
use App\Classes\APIResponse;
use App\Classes\Order;
use App\Classes\OrderProcessor\TypeBOrderProcessor;

test('process handles API exception by setting status to api_failure', function () {
    // Arrange
    $apiClient = Mockery::mock(APIClient::class);
    $apiClient->shouldReceive('callAPI')
        ->andThrow(new APIException('API error'));
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('api_failure');
});

test('process sets status to api_error when API returns non-success status', function () {
    // Arrange
    $apiClient = Mockery::mock(APIClient::class);
    $apiResponse = Mockery::mock(APIResponse::class);
    $apiResponse->status = 'error';
    
    $apiClient->shouldReceive('callAPI')
        ->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('api_error');
});

test('process sets status to processed when response is success, data >= 50 and amount < 100', function () {
    // Arrange
    $apiClient = Mockery::mock(APIClient::class);
    $apiResponse = Mockery::mock(APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 60;
    
    $apiClient->shouldReceive('callAPI')
        ->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 99, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('processed');
});

test('process sets status to pending when response is success but data < 50', function () {
    // Arrange
    $apiClient = Mockery::mock(APIClient::class);
    $apiResponse = Mockery::mock(APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 49;
    
    $apiClient->shouldReceive('callAPI')
        ->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 99, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('pending');
});

test('process sets status to pending when response is success and flag is true', function () {
    // Arrange
    $apiClient = Mockery::mock(APIClient::class);
    $apiResponse = Mockery::mock(APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 60;
    
    $apiClient->shouldReceive('callAPI')
        ->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 150, true);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('pending');
});

test('process sets status to error for other success response cases', function () {
    // Arrange
    $apiClient = Mockery::mock(APIClient::class);
    $apiResponse = Mockery::mock(APIResponse::class);
    $apiResponse->status = 'success';
    $apiResponse->data = 60;
    
    $apiClient->shouldReceive('callAPI')
        ->andReturn($apiResponse);
    
    $processor = new TypeBOrderProcessor($apiClient);
    $order = new Order(1, 'B', 150, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('error');
}); 