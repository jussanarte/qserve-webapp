<?php
// backend/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'qserve_db');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private $connection;

    public function getConnection() {
        $this->connection = null;
        try {
            $this->connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->connection->exec("set names utf8");
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
        return $this->connection;
    }
}