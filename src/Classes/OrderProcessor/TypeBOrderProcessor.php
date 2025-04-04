<?php

namespace App\Classes\OrderProcessor;

use App\Classes\APIClient;
use App\Classes\APIException;
use App\Classes\APIResponse;
use App\Classes\Order;

class TypeBOrderProcessor implements OrderProcessorInterface
{
    private APIClient $apiClient;

    public function __construct(APIClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function process(Order $order, int $userId): void
    {
        try {
            $apiResponse = $this->apiClient->callAPI($order->id);

            if ($apiResponse->status === 'success') {
                $this->handleSuccessAPIResponse($order, $apiResponse);
            } else {
                $order->status = 'api_error';
            }
        } catch (APIException $e) {
            $order->status = 'api_failure';
        }
    }

    private function handleSuccessAPIResponse(Order $order, APIResponse $apiResponse): void
    {
        if ($apiResponse->data >= 50 && $order->amount < 100) {
            $order->status = 'processed';
        } elseif ($apiResponse->data < 50 || $order->flag) {
            $order->status = 'pending';
        } else {
            $order->status = 'error';
        }
    }
} 