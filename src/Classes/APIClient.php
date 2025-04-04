<?php

namespace App\Classes;

interface APIClient
{
    public function callAPI($orderId): APIResponse;
} 