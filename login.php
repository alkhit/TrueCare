<?php include 'includes/config.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login to TrueCare</h4>
                </div>
                <div class="card-body p-4">
                    <form id="login-form" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="Enter your email">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="Enter your password">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>
                    <div class="text-center mt-4">
                        <p class="text-muted">Don't have an account? 
                            <a href="<?php echo BASE_URL; ?>/register.php" class="text-decoration-none fw-bold">Register here</a>
                        </p>
                    </div>
                    
                    <!-- Demo Accounts Info -->
                    <div class="alert alert-info mt-4">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Demo Accounts</h6>
                        <small>
                            <strong>Admin:</strong> admin@truecare.org / admin123<br>
                            <strong>Orphanage:</strong> Register to create<br>
                            <strong>Donor:</strong> Register to create
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Direct event listener for the login form
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Use the global trueCareApp instance if available, otherwise handle directly
            if (window.trueCareApp) {
                window.trueCareApp.handleLogin(e);
            } else {
                // Fallback direct handling
                handleLoginDirectly(e);
            }
        });
    }
    
    // Fallback function if trueCareApp isn't available
    async function handleLoginDirectly(event) {
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';

            const response = await fetch('<?php echo BASE_URL; ?>/src/auth/login_process.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast('Successfully logged in! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect || '<?php echo BASE_URL; ?>/src/auth/dashboard.php';
                }, 1000);
            } else {
                showToast(result.message || 'Login failed', 'error');
            }
        } catch (error) {
            console.error('Login error:', error);
            showToast('Network error. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
    
    function showToast(message, type = 'info') {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>