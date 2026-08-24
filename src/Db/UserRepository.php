<?php

declare(strict_types=1);

namespace App\Db;

use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        // store the injected PDO instance in the private property
        $this->pdo = $pdo;
    }

    
    public function createTable(): void
    {
        // prepare and execute the CREATE TABLE IF NOT EXISTS statement
        $sql = "
            DROP TABLE IF EXISTS users;
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                surname VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE
            );
        ";

        $this->pdo->exec($sql);
    }

    
    public function insertUser(string $name, string $surname, string $email): bool
    {
        $sql = "
            INSERT INTO users (name, surname, email)
            VALUES (:name, :surname, :email); 
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'name' => $name,
            'surname' => $surname,
            'email' => $email
        ]);
    }

    
    public function emailExists(string $email): bool
    {
        $sql = "
            SELECT 1 FROM users
            WHERE email = :email LIMIT 1;
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['email' => $email]);

        // grab a result from the sql query because
        // if it returns true, then there is an email that exists
        return $stmt->fetch() !== false;
    }
}
