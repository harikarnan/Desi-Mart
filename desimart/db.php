<?php
class Database {
    private $host = "localhost"; // MySQL host
    private $db_name = "desimart"; // Correct database name
    private $username = "root"; // Default username for XAMPP
    private $password = ""; // Default password for XAMPP (empty)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
