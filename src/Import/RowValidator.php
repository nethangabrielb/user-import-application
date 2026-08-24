<?php

declare(strict_types=1);

namespace App\Import;

class RowValidator
{
    public function validateRow(array $row): array                                                      
    {                                                                                                   
        $errors = [];                                                                                   
                                                                                                        
        // check shape                                                                               
        if (count($row) !== 3) {                                                                        
            $errors[] = "Invalid field shape";                                                          
            return $errors; 
        }                                                                                               
                                                                                                        
        // check for empty/whitespace-only fields for name, surname                                                
        if (trim($row[0]) === '') {
            $errors[] = "Invalid name";
        }
  
        if (trim($row[1]) === '') {
            $errors[] = "Invalid surname";
        }
  
        // check email format
        if (empty(trim($row[2])) || !filter_var(trim($row[2]), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
        }
  
        return $errors;
    }
}