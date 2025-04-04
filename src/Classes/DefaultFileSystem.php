<?php

namespace App\Classes;

class DefaultFileSystem implements FileSystemInterface
{
    public function openFile(string $filename, string $mode)
    {
        return fopen($filename, $mode);
    }

    public function writeCSV($fileHandle, array $data): void
    {
        fputcsv($fileHandle, $data);
    }

    public function closeFile($fileHandle): void
    {
        fclose($fileHandle);
    }
} 