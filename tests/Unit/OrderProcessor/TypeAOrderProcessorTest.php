<?php

use App\Classes\Order;
use App\Classes\OrderProcessor\TypeAOrderProcessor;

test('process sets status to exported when file creation succeeds', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(true);
    $fileSystem->shouldReceive('writeCSV');
    $fileSystem->shouldReceive('closeFile');
    
    $processor = new TypeAOrderProcessor($fileSystem);
    $order = new Order(1, 'A', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('exported');
});

test('process sets status to export_failed when file creation fails', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(false);
    
    $processor = new TypeAOrderProcessor($fileSystem);
    $order = new Order(1, 'A', 100, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('export_failed');
});

test('process adds high value note when order amount > 150', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(true);
    
    // Capturing the write calls for verification
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
    expect($order->status)->toBe('exported');
    expect(count($writeCallParams))->toBe(3); // Header, data, and note
    expect($writeCallParams[2][5])->toBe('High value order');
});

test('process validates exact boundary value 150 for high value note', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(true);
    
    // Capturing the write calls for verification
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
    expect($order->status)->toBe('exported');
    expect(count($writeCallParams))->toBe(2); // Only header and data, no note
});

test('process does not add high value note when order amount < 150', function () {
    // Arrange
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    $fileSystem->shouldReceive('openFile')->andReturn(true);
    
    // Capturing the write calls for verification
    $writeCallParams = [];
    $fileSystem->shouldReceive('writeCSV')
        ->andReturnUsing(function($fileHandle, $data) use (&$writeCallParams) {
            $writeCallParams[] = $data;
            return null;
        });
    
    $fileSystem->shouldReceive('closeFile');
    
    $processor = new TypeAOrderProcessor($fileSystem);
    $order = new Order(1, 'A', 149, false);
    
    // Act
    $processor->process($order, 1);
    
    // Assert
    expect($order->status)->toBe('exported');
    expect(count($writeCallParams))->toBe(2); // Only header and data, no note
}); 