// assets/js/app.js
class TrueCareApp {
    constructor(basePath) {
        this.baseUrl = basePath || window.location.origin;
        this.init();
    }

    init() {
        this.setupAuthForms();
        console.log('TrueCare App initialized');
    }

    setupAuthForms() {
        const login = document.getElementById('login-form');
        if (login) login.addEventListener('submit', (e) => this.handleLogin(e));

        const register = document.getElementById('register-form');
        if (register) register.addEventListener('submit', (e) => this.handleRegister(e));
    }

    async handleLogin(evt) {
        evt.preventDefault();
        const form = evt.target;
        const submit = form.querySelector('button[type="submit"]');
        const data = new FormData(form);

        const original = submit.innerHTML;
        submit.disabled = true;
        submit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in...';

        try {
            const res = await fetch('src/auth/login_process.php', {
                method: 'POST',
                body: data
            });
            
            const json = await res.json();

            if (json.success) {
                this.showToast(json.message || 'Login successful!', 'success');
                // Use setTimeout to ensure toast is visible before redirect
                setTimeout(() => {
                    window.location.href = json.redirect;
                }, 1000);
            } else {
                this.showToast(json.message || 'Login failed. Please try again.', 'danger');
            }
        } catch (err) {
            console.error('Login error:', err);
            this.showToast('Network error. Please check your connection.', 'danger');
        } finally {
            submit.disabled = false;
            submit.innerHTML = original;
        }
    }

    async handleRegister(evt) {
        evt.preventDefault();
        const form = evt.target;
        const submit = form.querySelector('button[type="submit"]');
        const data = new FormData(form);
        
        const original = submit.innerHTML;
        submit.disabled = true;
        submit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating account...';

        // Client-side validation
        const password = form.querySelector('#password')?.value;
        const confirmPassword = form.querySelector('#confirm_password')?.value;
        
        if (password !== confirmPassword) {
            this.showToast('Passwords do not match', 'danger');
            submit.disabled = false;
            submit.innerHTML = original;
            return;
        }

        if (password.length < 6) {
            this.showToast('Password must be at least 6 characters', 'danger');
            submit.disabled = false;
            submit.innerHTML = original;
            return;
        }

        try {
            const res = await fetch('src/auth/register_process.php', {
                method: 'POST',
                body: data
            });
            
            const json = await res.json();

            if (json.success) {
                this.showToast(json.message || 'Account created successfully!', 'success');
                // Automatic redirect after successful registration
                setTimeout(() => {
                    window.location.href = json.redirect;
                }, 1500);
            } else {
                this.showToast(json.message || 'Registration failed. Please try again.', 'danger');
            }
        } catch (err) {
            console.error('Registration error:', err);
            this.showToast('Network error. Please check your connection.', 'danger');
        } finally {
            submit.disabled = false;
            submit.innerHTML = original;
        }
    }

    showToast(message, level = 'info') {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.truecare-toast');
        existingToasts.forEach(toast => toast.remove());

        const wrapper = document.createElement('div');
        wrapper.className = `truecare-toast position-fixed top-0 end-0 m-3`;
        wrapper.style.zIndex = 9999;
        
        const bgClass = level === 'success' ? 'success' : 
                       level === 'danger' ? 'danger' : 
                       level === 'warning' ? 'warning' : 'info';
        
        wrapper.innerHTML = `
            <div class="toast align-items-center text-bg-${bgClass} border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        document.body.appendChild(wrapper);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (wrapper.parentNode) {
                wrapper.remove();
            }
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.trueCareApp = new TrueCareApp();
});