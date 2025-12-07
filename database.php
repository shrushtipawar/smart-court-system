<?php
class Database {
    public $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=justiceflow", "root", "");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES utf8mb4");
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
?>