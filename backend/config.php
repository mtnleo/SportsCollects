<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'sportmarket_db';
    private $username = 'root';
    private $password = '';
    private $conn = null;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Connection Error: ' . $e->getMessage()]);
            exit();
        }
    }
}
