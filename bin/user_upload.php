#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Csv\CsvReader;
use App\Db\Connection;
use App\Db\UserRepository;
use App\Import\ImportService;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt("", ["file:", "create-table", "help", "dry-run"]);

if (isset($options["help"])) {
  echo <<<HELP
    User Import CLI Application                                                                                                                                                                                                
                                                                                                                                                                                                                               
    Usage:                                                                                                                                                                                                                     
      php bin/user_upload.php [options]                                                                                                                                                                                        
                                                                                                                                                                                                                               
    Options:                                                                                                                                                                                                                   
      --file <filename>   The CSV file to process                                                                                                                                                                              
      --dry-run           Parse and validate data without inserting into database                                                                                                                                              
      --create-table      Create/rebuild the 'users' table in PostgreSQL
      --help              Display this help message
  
    Examples:
      php bin/user_upload.php --create-table
      php bin/user_upload.php --file samples/users.csv --dry-run
      php bin/user_upload.php --file samples/users.csv
  
    HELP;
        exit(0);
}


if (isset($options["create-table"])) {
  try {

    $connection = new Connection();
    $pdo = $connection->getPdo();
    $repo = new UserRepository($pdo);
    $repo->createTable();
    echo "Table 'users' created/rebuilt successfully.\n";
  } catch (\PDOException $e) {
    fwrite(STDERR, "Database Error: " . $e->getMessage() . "\n");                                                                                                                                                      
    exit(1); 
  }

  if (!isset($options["file"])) {
    exit(0);
  }
}

if (isset($options["file"])) {
  $filePath = $options["file"];

  // check if file exists and is readable
  if (!file_exists($filePath) || !is_readable($filePath)) {                                                                                                                                                                  
      fwrite(STDERR, "Error: File '{$filePath}' does not exist or is not readable.\n");                                                                                                                                      
      exit(1);                                                                                                                                                                                                               
  }

  // initialize csv reader and read files in csv
  $csvReader = new CsvReader();
  $rows = $csvReader->read($filePath);

  $isDryRun = isset($options["dry-run"]);

  $userRepository = null;

  // initialize db connection and get pdo
  try {
    $connection = new Connection();
    $pdo = $connection->getPdo();
    $userRepository = new UserRepository($pdo);
  } catch (\PDOException $e) {
    if (!isDryRun) {
      fwrite(STDERR, "Database Connection Error: " . $e->getMessage() . "\n");
      exit(1);
    }
    echo "Note: Running dry-run mode without database connection.\n"; 
  }

  // initialize and process rows in importService
  $importService = new ImportService(userRepository: $userRepository);
  $result = $importService->process($rows, $isDryRun);

  // output Summary Report                                                                                                                                                                                               
  echo "\n" . str_repeat('=', 50) . "\n";                                                                                                                                                                                
  echo $isDryRun ? "MODE: DRY RUN (No records inserted into DB)\n" : "MODE: LIVE IMPORT\n";                                                                                                                              
  echo str_repeat('=', 50) . "\n";                                                                                                                                                                                       
                                                                                                                                                                                                                          
  echo "Total processed : " . $result->getTotalProcessed() . "\n";                                                                                                                                                       
  echo "Imported / Valid: " . $result->getImportedCount() . "\n";                                                                                                                                                        
  echo "Duplicates      : " . $result->getDuplicateCount() . "\n";                                                                                                                                                       
  echo "Invalid records : " . $result->getInvalidCount() . "\n";                                                                                                                                                         
  echo str_repeat('-', 50) . "\n";                                                                                                                                                                                       
                                                                                                                                                                                                                          
  // print duplicate records if any                                                                                                                                                                                      
  if ($result->getDuplicateCount() > 0) {                                                                                                                                                                                
      echo "\nDUPLICATE RECORDS:\n";                                                                                                                                                                                     
      foreach ($result->getDuplicates() as $dupe) {                                                                                                                                                                      
          echo " - Line {$dupe['rowNumber']}: {$dupe['email']}\n";                                                                                                                                                       
      }                                                                                                                                                                                                                  
  }                                                                                                                                                                                                                      
                                                                                                                                                                                                                          
  // print invalid records with reasons if any
  if ($result->getInvalidCount() > 0) {
      echo "\nINVALID RECORDS:\n";
      foreach ($result->getInvalid() as $inv) {
          $reasons = implode(', ', $inv['errors']);
          $data = implode(', ', $inv['data']);
          echo " - Line {$inv['rowNumber']}: [{$data}] -> {$reasons}\n";
      }
  }

  echo "\n" . str_repeat('=', 50) . "\n\n";
}

if (empty($options)) {
    fwrite(STDERR, "Error: No options provided. Use --help for usage instructions.\n");
    exit(1);
}
