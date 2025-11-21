    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-hands-helping me-2"></i>TrueCare</h5>
                    <p class="text-muted">Connecting generous donors with orphanages in need. Together we can make a difference in children's lives.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>/index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/src/campaigns/campaigns.php" class="text-muted text-decoration-none">Campaigns</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6>Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6>Contact Info</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Nairobi, Kenya</li>
                        <li><i class="fas fa-phone me-2"></i> +254 700 000 000</li>
                        <li><i class="fas fa-envelope me-2"></i> info@truecare.org</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2024 TrueCare. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">Made with <i class="fas fa-heart text-danger"></i> for a better world</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
    
    <script>
        // Global TrueCare App Object
        window.trueCareApp = {
            baseUrl: '<?php echo BASE_URL; ?>',
            showToast: function(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 5000);
            },
            
            formatCurrency: function(amount) {
                return 'Ksh ' + parseInt(amount).toLocaleString();
            },
            
            handleLogin: function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                
                fetch(this.baseUrl + '/src/auth/login_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showToast('Login successful! Redirecting...', 'success');
                        setTimeout(() => {
                            window.location.href = data.redirect || this.baseUrl + '/src/auth/dashboard.php';
                        }, 1000);
                    } else {
                        this.showToast(data.message || 'Login failed', 'error');
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    this.showToast('Network error. Please try again.', 'error');
                });
            },
            
            handleRegister: function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                
                fetch(this.baseUrl + '/src/auth/register_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text();
                    }
                })
                .then(data => {
                    if (data) {
                        // If we get here, there was an error
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    this.showToast('Network error. Please try again.', 'error');
                });
            }
        };

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>