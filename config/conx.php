<?php

class Database {
    private static $instance = null; // Singleton instance we did  it static to be accessible without instantiating the class and null to check if an instance already exists
    private $pdo;

    private function __construct($dsn, $username ,  $password){
        try{
            $this->pdo = new PDO($dsn , $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

        }catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed.");
        }
    }

    public static function getIstance($dsn ,$username, $password){
        if(self::$instance === null){
            $dsn = 'mysql:host=localhost;dbname=buymatch;charset=utf8mb4';
            $username = $username ?? 'root';
            $password = $password ?? '';
            self::$instance = new self($dsn, $username, $password); // new self() calls the private constructor
        }
        return self::$instance;
    }

    public function getConnection(){
        return $this->pdo;
    }
}