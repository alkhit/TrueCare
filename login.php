<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_SESSION['user_id'])) {
    // User is already logged in → redirect based on role
    $role = $_SESSION['role'] ?? 'donor';
    if ($role === 'donor') {
        header("Location: src/auth/donor_dashboard.php");
    } else {
        header("Location: src/auth/orphanage_dashboard.php");
    }
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$page_title = "Login - TrueCare";
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3><i class="fas fa-sign-in-alt me-2"></i>Login to TrueCare</h3>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="src/auth/login_process.php">
                        <div class="mb-3">
                            <label>Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required placeholder="Enter email">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label>Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" required placeholder="Enter password">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="mb-0">
                                Don't have an account? <a href="register.php" class="fw-bold text-decoration-none">Register</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
