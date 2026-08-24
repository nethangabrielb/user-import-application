<?php

declare(strict_types=1);

use App\Csv\CsvReader;
use App\Db\Connection;
use App\Db\UserRepository;
use App\Import\ImportService;

require __DIR__ . '/../vendor/autoload.php';

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit();
}


$httpMethod = $_SERVER['REQUEST_METHOD'];
$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($httpMethod === "POST" && $urlPath === "/api/preview") {
  $file = $_FILES['file'] ?? null;

  // handle errors if file doesnt exist and not readable
  if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    
    echo json_encode([
      'error' => 'No valid file was uploaded'
    ]);

    exit;
  }
  
  // read the rows from the file using CsvReader
  $reader = new CsvReader();
  $rows = $reader->read($file['tmp_name']);

  // proeprlyt initialize user repository 
  $userRepository = null;
  try {
    $connection = new Connection();
    $pdo = $connection->getPdo();
    $userRepository = new UserRepository($pdo);
  } catch (\PDOException $e) {
    http_response_code(500);
    
    echo json_encode([
      'error' => "Database Error: " . $e->getMessage() . "\n"
    ]);

    exit; 
  }

  // initialize service with user repository for dupes checking
  // and initiate dry run
  $importService = new ImportService(userRepository: $userRepository);
  $result = $importService->process($rows, isDryRun: true);
  
  http_response_code(200);
  echo json_encode($result->toArray());
  exit;
}




