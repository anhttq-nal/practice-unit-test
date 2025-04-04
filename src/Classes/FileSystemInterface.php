<?php

namespace App\Classes;

interface FileSystemInterface
{
    public function openFile(string $filename, string $mode);
    public function writeCSV($fileHandle, array $data): void;
    public function closeFile($fileHandle): void;
} 