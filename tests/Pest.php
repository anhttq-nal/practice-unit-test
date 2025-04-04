<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Create a mock for FileSystemInterface that returns predictable values
 */
function mockFileSystem(bool $fileOpenSuccess = true, bool $shouldAddHighValueNote = false)
{
    $fileSystem = Mockery::mock(\App\Classes\FileSystemInterface::class);
    
    $fileSystem->shouldReceive('openFile')
        ->andReturn($fileOpenSuccess);
    
    $fileSystem->shouldReceive('writeCSV');
    
    $fileSystem->shouldReceive('closeFile');
    
    return $fileSystem;
}

/**
 * Create a mock for APIClient that returns a specific response
 */
function mockAPIClient($status = 'success', $data = 60, $shouldThrowException = false)
{
    $apiClient = Mockery::mock(\App\Classes\APIClient::class);
    
    if ($shouldThrowException) {
        $apiClient->shouldReceive('callAPI')
            ->andThrow(new \App\Classes\APIException('API error'));
    } else {
        $apiResponse = Mockery::mock(\App\Classes\APIResponse::class);
        $apiResponse->status = $status;
        $apiResponse->data = $data;
        
        $apiClient->shouldReceive('callAPI')
            ->andReturn($apiResponse);
    }
    
    return $apiClient;
}

/**
 * Create a mock for DatabaseService with configurable behavior
 */
function mockDatabaseService(array $orders = [], bool $updateSuccess = true, bool $shouldThrowException = false)
{
    $dbService = Mockery::mock(\App\Classes\DatabaseService::class);
    
    $dbService->shouldReceive('getOrdersByUser')
        ->andReturn($orders);
    
    if ($shouldThrowException) {
        $dbService->shouldReceive('updateOrderStatus')
            ->andThrow(new \App\Classes\DatabaseException('Database error'));
    } else {
        $dbService->shouldReceive('updateOrderStatus')
            ->andReturn($updateSuccess);
    }
    
    return $dbService;
} 