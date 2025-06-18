<?php

class Database {
    private $host = "localhost";
    private $db_name = "tasks";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect () {
        try {
            $this->conn = new PDO (
                "mysql:host=$this->host;dbname=$this->db_name;charset=utf8",
                $this->username,
                $this->password
            );

            // Set error reporting
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit;
        }
    }
}

?>