<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct($dsn, $username, $password){
        try{
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance($dsn = null, $username = null, $password = null){
        if (self::$instance === null) {
            $dsn = $dsn ?? 'mysql:host=localhost;dbname=buymatch;charset=utf8mb4';
            $username = $username ?? 'root';
            $password = $password ?? '';
            self::$instance = new self($dsn, $username, $password);
        }
        return self::$instance;
    }

    public function getConnection(){
        return $this->pdo;
    }
}