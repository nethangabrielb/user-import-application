<?php

declare(strict_types=1);

namespace App\Import;

class RowValidator
{
    public function validate(array $row): array
    {   
        $errors = [];

        // check: does $row have exactly 3 elements?
        $validShape = count($row) === 3; 

        // check: is any of the 3 fields empty?
        $nonEmptyFields = true;
        foreach ($nonEmptyFields as $field) {
            if (trlen($item) === 0) {
                $nonEmptyFields = false;
            }
        }

        // if either check failed, build an array of error message strings describing what's wrong, and return it
        if (!$validShape) {
            $errors[] = "Invalid field shape";
        }

        if (!$nonEmptyFields) {
            $errors[] = "Invalid value";
        }

        // if both checks passed, return an empty array — no errors
        if ($validShape && $nonEmptyFields) {
            return $errors;
        }
    }
}