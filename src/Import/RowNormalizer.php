<?php

declare(strict_types=1);

namespace App\Import;

class RowNormalizer
{
    public function normalize(array $row): array
    {
        $updatedArray = [];

        // capitalize first name
        $updatedArray[] = ucfirst(strtolower(trim($row[0])));

        // capitalize surname
        $updatedArray[] = ucfirst(strtolower(trim($row[1])));

        // lowercase email
        $updatedArray[] = strtolower(trim($row[2]));

        // return a new array with the 3 normalized values, same order
        return $updatedArray;
    }
}