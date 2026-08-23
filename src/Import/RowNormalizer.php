<?php

declare(strict_types=1);

namespace App\Import;

class RowNormalizer
{
    public function normalize(array $row): array
    {
        $updatedArray = [];

        // capitalize first name
        $updatedArray[] = ucfirst(strtolower($row[0])).trim();

        // capitalize surname
        $updatedArray[] = ucfirst(strtolower($row[1])).trim();;

        // lowercase email
        $updatedArray[] = strtolower($row[2]).trim();

        // return a new array with the 3 normalized values, same order
        return $updatedArray;
    }
}