<?php

declare(strict_types=1);

namespace App\Db;

use Dotenv\Dotenv;

use PDO;

class Connection
{
    private PDO $pdo;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $host     = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port     = $_ENV['DB_PORT'] ?? '5432';
        $db       = $_ENV['DB_NAME'] ?? '';
        $user     = $_ENV['DB_USER'] ?? '';
        $password = $_ENV['DB_PASS'] ?? '';

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $user, $password, $options);
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}