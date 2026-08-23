<?php

declare(strict_types=1);

namespace App\Csv;

class CsvReader
{
    public function read(string $filePath): array 
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("File at {$filePath} does not exist/not readable.");
        } 
        
        $rows = [];

        $handle = fopen($filePath, 'r');

        // skip the header row
        $headers = fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (!$row) {
                continue;
            } else {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }
}
