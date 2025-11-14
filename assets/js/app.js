// TrueCare Portal - Main JavaScript Application
class TrueCareApp {
    constructor() {
        this.baseUrl = window.location.origin + '/truecare-portal';
        this.init();
    }

    init() {
        this.setupEventListeners();
        console.log('TrueCare App initialized');
    }

    setupEventListeners() {
        // Setup form submissions
        this.handleAuthForms();
        
        // Setup navigation
        this.setupNavigation();
    }

    handleAuthForms() {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
            console.log('Login form handler attached');
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
            console.log('Register form handler attached');
        }
    }

    setupNavigation() {
        // Handle navigation links
        const navLinks = document.querySelectorAll('a[href]');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Allow default behavior for external links or links with targets
                if (link.target || link.href.startsWith('http') && !link.href.includes(window.location.host)) {
                    return;
                }
                
                // For internal navigation, ensure smooth transition
                const href = link.getAttribute('href');
                if (href && !href.startsWith('#')) {
                    // Add loading state if needed
                    link.classList.add('loading');
                }
            });
        });
    }

    async handleLogin(event) {
        event.preventDefault();
        console.log('Login form submitted');
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';

            const response = await fetch(this.baseUrl + '/src/auth/login_process.php', {
                method: 'POST',
                body: formData
            });

            console.log('Login response status:', response.status);
            const result = await response.json();
            console.log('Login result:', result);

            if (result.success) {
                this.showToast(result.message || 'Successfully logged in!', 'success');
                
                // Redirect after a brief delay
                setTimeout(() => {
                    window.location.href = result.redirect || this.baseUrl + '/src/auth/dashboard.php';
                }, 1000);
            } else {
                this.showToast(result.message || 'Login failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showToast('Network error. Please check your connection and try again.', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    async handleRegister(event) {
        event.preventDefault();
        console.log('Register form submitted');
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Basic client-side validation
        const password = form.querySelector('#password').value;
        const confirmPassword = form.querySelector('#confirm_password').value;
        const terms = form.querySelector('#terms');

        if (password !== confirmPassword) {
            this.showToast('Passwords do not match!', 'error');
            return;
        }

        if (password.length < 6) {
            this.showToast('Password must be at least 6 characters long!', 'error');
            return;
        }

        if (terms && !terms.checked) {
            this.showToast('Please agree to the Terms of Service!', 'error');
            return;
        }

        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';

            const response = await fetch(this.baseUrl + '/src/auth/register_process.php', {
                method: 'POST',
                body: formData
            });

            console.log('Register response status:', response.status);
            const result = await response.json();
            console.log('Register result:', result);

            if (result.success) {
                this.showToast(result.message || 'Account created successfully!', 'success');
                
                // Redirect after a brief delay
                setTimeout(() => {
                    window.location.href = result.redirect || this.baseUrl + '/login.php';
                }, 1500);
            } else {
                this.showToast(result.message || 'Registration failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Registration error:', error);
            this.showToast('Network error. Please check your connection and try again.', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    showToast(message, type = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.truecare-toast');
        existingToasts.forEach(toast => toast.remove());

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `truecare-toast alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'error' ? 'fa-exclamation-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${icon} me-2"></i>
                <span class="flex-grow-1">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Utility functions
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES'
        }).format(amount);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-KE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.trueCareApp = new TrueCareApp();
    console.log('TrueCare App loaded successfully');
});

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add some basic CSS for the toast
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .truecare-toast {
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .btn.loading {
        position: relative;
        color: transparent;
    }
    
    .btn.loading::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-right-color: transparent;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
`;
document.head.appendChild(toastStyles);// TrueCare Portal - Main JavaScript Application
class TrueCareApp {
    constructor() {
        this.baseUrl = window.location.origin + '/truecare-portal';
        this.init();
    }

    init() {
        this.setupEventListeners();
        console.log('TrueCare App initialized');
    }

    setupEventListeners() {
        // Setup form submissions
        this.handleAuthForms();
        
        // Setup navigation
        this.setupNavigation();
    }

    handleAuthForms() {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
            console.log('Login form handler attached');
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
            console.log('Register form handler attached');
        }
    }

    setupNavigation() {
        // Handle navigation links
        const navLinks = document.querySelectorAll('a[href]');
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Allow default behavior for external links or links with targets
                if (link.target || link.href.startsWith('http') && !link.href.includes(window.location.host)) {
                    return;
                }
                
                // For internal navigation, ensure smooth transition
                const href = link.getAttribute('href');
                if (href && !href.startsWith('#')) {
                    // Add loading state if needed
                    link.classList.add('loading');
                }
            });
        });
    }

    async handleLogin(event) {
        event.preventDefault();
        console.log('Login form submitted');
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';

            const response = await fetch(this.baseUrl + '/src/auth/login_process.php', {
                method: 'POST',
                body: formData
            });

            console.log('Login response status:', response.status);
            const result = await response.json();
            console.log('Login result:', result);

            if (result.success) {
                this.showToast(result.message || 'Successfully logged in!', 'success');
                
                // Redirect after a brief delay
                setTimeout(() => {
                    window.location.href = result.redirect || this.baseUrl + '/src/auth/dashboard.php';
                }, 1000);
            } else {
                this.showToast(result.message || 'Login failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showToast('Network error. Please check your connection and try again.', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    async handleRegister(event) {
        event.preventDefault();
        console.log('Register form submitted');
        
        const form = event.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Basic client-side validation
        const password = form.querySelector('#password').value;
        const confirmPassword = form.querySelector('#confirm_password').value;
        const terms = form.querySelector('#terms');

        if (password !== confirmPassword) {
            this.showToast('Passwords do not match!', 'error');
            return;
        }

        if (password.length < 6) {
            this.showToast('Password must be at least 6 characters long!', 'error');
            return;
        }

        if (terms && !terms.checked) {
            this.showToast('Please agree to the Terms of Service!', 'error');
            return;
        }

        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';

            const response = await fetch(this.baseUrl + '/src/auth/register_process.php', {
                method: 'POST',
                body: formData
            });

            console.log('Register response status:', response.status);
            const result = await response.json();
            console.log('Register result:', result);

            if (result.success) {
                this.showToast(result.message || 'Account created successfully!', 'success');
                
                // Redirect after a brief delay
                setTimeout(() => {
                    window.location.href = result.redirect || this.baseUrl + '/login.php';
                }, 1500);
            } else {
                this.showToast(result.message || 'Registration failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Registration error:', error);
            this.showToast('Network error. Please check your connection and try again.', 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    showToast(message, type = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.truecare-toast');
        existingToasts.forEach(toast => toast.remove());

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `truecare-toast alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'error' ? 'fa-exclamation-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${icon} me-2"></i>
                <span class="flex-grow-1">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Utility functions
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES'
        }).format(amount);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-KE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.trueCareApp = new TrueCareApp();
    console.log('TrueCare App loaded successfully');
});

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add some basic CSS for the toast
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .truecare-toast {
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .btn.loading {
        position: relative;
        color: transparent;
    }
    
    .btn.loading::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-right-color: transparent;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
`;
document.head.appendChild(toastStyles);