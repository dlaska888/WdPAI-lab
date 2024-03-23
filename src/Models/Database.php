<?php

namespace LinkyApp\Models;

use PDO;

require_once "config.php";

class Database
{
    private string $username;
    private string $password;
    private string $host;
    private string $port;
    private string $database;
    private ?PDO $pdo;

    private static ?Database $instance = null;

    private function __construct()
    {
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->host = HOST;
        $this->port = PORT;
        $this->database = DATABASE;
        $this->pdo = $this->getConnection();
    }

    // Singleton design pattern
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function connect(): PDO
    {
        return $this->pdo;
    }
    
    private function getConnection() : PDO
    {
        $pdo = new PDO(
            "pgsql:host=$this->host;port=$this->port;dbname=$this->database",
            $this->username,
            $this->password,
            ["sslmode" => "prefer"]
        );

        // set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}
