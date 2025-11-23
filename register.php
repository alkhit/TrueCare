<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect if logged in
if (isset($_SESSION['user_id'])) {
    header("Location: src/auth/dashboard.php");
    exit;
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Ensure $db is defined
if (!isset($db)) {
    try {
        $database = new Database();
        $db = $database->getConnection();
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Flash data
$error = $_SESSION['error'] ?? '';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error'], $_SESSION['form_data']);

$page_title = "Register - TrueCare";
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3><i class="fas fa-user-plus me-2"></i>Join TrueCare</h3>
                    <p class="mb-0">Create your account and start making a difference</p>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="src/auth/register_process.php" id="register-form">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" class="form-control" required
                                           value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>"
                                           placeholder="Full Name">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" required
                                           value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                                           placeholder="Email">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" name="phone" class="form-control"
                                           value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"
                                           placeholder="0712345678">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role *</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Select Role</option>
                                    <option value="donor" <?php echo (($form_data['role'] ?? '') === 'donor') ? 'selected' : ''; ?>>Donor</option>
                                    <option value="orphanage" <?php echo (($form_data['role'] ?? '') === 'orphanage') ? 'selected' : ''; ?>>Orphanage</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" required placeholder="Password" minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm Password" minlength="6">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="terms" class="form-check-input" id="terms" required>
                            <label for="terms" class="form-check-label">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>

                        <div class="text-center">
                            Already have an account? <a href="login.php" class="text-success fw-bold">Login here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
