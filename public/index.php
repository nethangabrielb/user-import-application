<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// TODO: front controller for the React app to call, e.g.
//   POST /api/preview  -> parse + validate, return preview JSON
//   POST /api/import   -> import the valid rows, return result JSON
// Dispatch on $_SERVER['REQUEST_METHOD'] / $_SERVER['REQUEST_URI'].
