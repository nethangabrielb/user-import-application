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
                                                                                                        
        // check for empty/whitespace-only fields                                                    
        foreach ($row as $field) {
            if (strlen(trim($field)) === 0) {
                $errors[] = "Invalid value";
                break; 
            }
        }
  
        // check email format
        if (!empty(trim($row[2])) && !filter_var(trim($row[2]), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
        }
  
        return $errors;
    }
}