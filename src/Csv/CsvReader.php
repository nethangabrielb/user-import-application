<?php

declare(strict_types=1);

namespace App\Csv;

class CsvReader
{
    public function read($file): void 
    {

    // TODO
    $handle = fopen($file, 'r');
    
    while (($row = fgetcsv($handle)) !== false) {
        print_r($row);
    }
    
        fclose($handle);
    }
}
