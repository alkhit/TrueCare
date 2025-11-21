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
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Database Error - TrueCare</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-header bg-danger text-white text-center">
                                <h4><i class="fas fa-database me-2"></i>Database Connection Error</h4>
                            </div>
                            <div class="card-body text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                                <p class="text-danger">$message</p>
                                <p>Please check your database configuration and try again.</p>
                                <a href="../index.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-home me-2"></i>Return to Home
                                </a>
                            </div>
                        </div>
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

// Utility Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function checkAuth($required_role = null) {
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/login.php');
    }
    
    if ($required_role && getUserRole() !== $required_role) {
        $_SESSION['error'] = "You don't have permission to access that page.";
        redirect(BASE_URL . '/src/auth/dashboard.php');
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatCurrency($amount) {
    return 'Ksh ' . number_format($amount);
}

function showAlert($type, $message) {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show">
        <i class="fas fa-' . ($type === 'success' ? 'check-circle' : 'exclamation-triangle') . ' me-2"></i>
        ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

// Path helper function
function url($path = '') {
    $base_url = BASE_URL;
    $path = ltrim($path, '/');
    return $base_url . '/' . $path;
}

// Alternative: Absolute path helper
function abs_path($path = '') {
    return '/TrueCare/' . ltrim($path, '/');
}
?>