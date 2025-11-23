<?php
// Define the root directory path
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

// Database Configuration
class Database {
    private $host = 'localhost';
    private $db_name = 'truecare_portal';
    private $username = 'root';
    private $password = '';
    private $port = '3306';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host .
                ";port=" . $this->port .
                ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            $this->showErrorPage($exception->getMessage());
        }
        return $this->conn;
    }

    private function showErrorPage($message) {
        $html = "<html><body><h1>Database Error</h1><p>$message</p></body></html>";
        die($html);
    }
} // <-- Close Database class

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . "://" . $host . $script_path;
$base_url = rtrim($base_url, '/');
define('BASE_URL', $base_url);

// Define database constants for compatibility
define('DB_HOST', 'localhost');
define('DB_NAME', 'truecare_portal');
define('DB_USER', 'root');
define('DB_PASS', '');