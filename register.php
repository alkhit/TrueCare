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

require_once __DIR__ . '/includes/config.php';

// Get error and form data
$error = $_SESSION['error'] ?? '';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error']);
unset($_SESSION['form_data']);

$page_title = "Register - TrueCare";
$show_navbar = true;
include __DIR__ . '/includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3><i class="fas fa-user-plus me-2"></i>Join TrueCare</h3>
                    <p class="mb-0">Create your account and start making a difference</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form id="register-form" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" required 
                                               value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>"
                                               placeholder="Enter your full name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required 
                                               value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                                               placeholder="Enter your email">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"
                                               placeholder="e.g., 0712345678">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">I want to join as *</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">Select your role</option>
                                        <option value="donor" <?php echo ($form_data['role'] ?? '') === 'donor' ? 'selected' : ''; ?>>Donor (I want to support campaigns)</option>
                                        <option value="orphanage" <?php echo ($form_data['role'] ?? '') === 'orphanage' ? 'selected' : ''; ?>>Orphanage (I want to create campaigns)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required 
                                               placeholder="Create a password (min. 6 characters)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                                               placeholder="Confirm your password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a> *
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">
                            Already have an account? 
                            <a href="login.php" class="text-decoration-none fw-bold">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            window.trueCareApp.handleRegister(e);
        });
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>