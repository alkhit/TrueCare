<?php
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
            // Don't auto-setup for now, just show error
            $this->showErrorPage($exception->getMessage());
        }

        return $this->conn;
    }

    private function showErrorPage($message) {
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Database Error</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4>Database Connection Error</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-danger">$message</p>
                        <p>Please check your database configuration.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
HTML;
        die($html);
    }
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL configuration - SIMPLIFIED
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . "://" . $host . $script_path;
$base_url = rtrim($base_url, '/');

define('BASE_URL', $base_url);

// Utility function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Utility function to get user role
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

// Utility function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}
?>