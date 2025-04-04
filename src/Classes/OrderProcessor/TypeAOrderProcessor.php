<?php

namespace App\Classes\OrderProcessor;

use App\Classes\FileSystemInterface;
use App\Classes\Order;

class TypeAOrderProcessor implements OrderProcessorInterface
{
    private FileSystemInterface $fileSystem;

    public function __construct(FileSystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function process(Order $order, int $userId): void
    {
        $csvFile = 'orders_type_A_' . $userId . '_' . time() . '.csv';
        $fileHandle = $this->fileSystem->openFile($csvFile, 'w');
        
        if ($fileHandle !== false) {
            $this->fileSystem->writeCSV($fileHandle, ['ID', 'Type', 'Amount', 'Flag', 'Status', 'Priority']);

            $this->fileSystem->writeCSV($fileHandle, [
                $order->id,
                $order->type,
                $order->amount,
                $order->flag ? 'true' : 'false',
                $order->status,
                $order->priority
            ]);

            if ($order->amount > 150) {
                $this->fileSystem->writeCSV($fileHandle, ['', '', '', '', 'Note', 'High value order']);
            }

            $this->fileSystem->closeFile($fileHandle);
            $order->status = 'exported';
        } else {
            $order->status = 'export_failed';
        }
    }
} 