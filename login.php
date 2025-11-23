<?php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: src/auth/dashboard.php");
    exit;
}

// Use absolute path for config
require_once __DIR__ . '/includes/config.php';

// Get error/success messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';

// Clear messages after displaying
unset($_SESSION['error']);
unset($_SESSION['success']);

$page_title = "Login - TrueCare";
$show_navbar = true;
include __DIR__ . '/includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3><i class="fas fa-sign-in-alt me-2"></i>Login to TrueCare</h3>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form id="login-form" method="POST" action="src/auth/login_process.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="Enter your email">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="Enter your password">
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-2">
                            <a href="forgot_password.php" class="text-decoration-none">Forgot your password?</a>
                        </p>
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="register.php" class="text-decoration-none fw-bold">Register here</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            window.trueCareApp.handleLogin(e);
        });
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>