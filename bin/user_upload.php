#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Csv\CsvReader;

require __DIR__ . '/../vendor/autoload.php';

// TODO: parse $argv for --file <filename>, --dry-run, --create-table, --help
if (in_array("--file", $argv)) {
  $fileArgIndex = array_search('--file', $argv);
  $file = $argv[$fileArgIndex + 1] ?? null;

  $reader = new CsvReader();

  if ($file) {
    $reader->read($file);
  }
}
// TODO: call App\Import\ImportService with the parsed options
// TODO: print a report to STDOUT (should mirror the web UI's preview format)
