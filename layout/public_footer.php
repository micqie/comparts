<?php
// Public footer
?>
<footer class="public-footer py-4 text-center text-white">
    <div class="container">
        <div class="mb-2">
            <a href="https://www.intel.com" class="text-white-50 me-3" target="_blank" rel="noopener">Intel</a>
            <a href="https://www.amd.com" class="text-white-50 me-3" target="_blank" rel="noopener">AMD</a>
            <a href="https://www.nvidia.com" class="text-white-50" target="_blank" rel="noopener">NVIDIA</a>
        </div>
        <small class="text-white-50">© <?php echo date('Y'); ?> Comparts. All rights reserved.</small>
    </div>
</footer>

<!-- Auth Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="authModalHeader">
                <h5 class="modal-title" id="authModalLabel">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="authErrorAlert" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <span id="authErrorText"></span>
                </div>
                <div id="authSuccessAlert" class="alert alert-success d-none" role="alert">
                    <i class="bi bi-check-circle"></i> <span id="authSuccessText"></span>
                </div>

                <!-- Login Form -->
                <div id="loginForm">
                    <form method="POST" action="index.php?module=auth&action=process_login" id="loginFormElement">
                        <div class="mb-3">
                            <label for="modal_login_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="modal_login_username" name="username" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="modal_login_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="modal_login_password" name="password" required>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                        <div class="text-center">
                            <p class="mb-0">Don't have an account?
                                <a href="#" class="text-decoration-none" onclick="event.preventDefault(); showRegisterForm(); return false;">
                                    <strong>Register here</strong>
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Register Form -->
                <div id="registerForm" style="display: none;">
                    <form method="POST" action="index.php?module=auth&action=process_register" id="registerFormElement">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_reg_username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="modal_reg_username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_reg_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="modal_reg_email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_reg_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="modal_reg_password" name="password" required>
                                <small class="text-muted">Any password is fine.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_reg_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="modal_reg_confirm" name="confirm_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_reg_full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="modal_reg_full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_reg_contact" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="modal_reg_contact" name="contact_number">
                            </div>
                            <div class="col-12">
                                <label for="modal_reg_address" class="form-label">Address</label>
                                <textarea class="form-control" id="modal_reg_address" name="address" rows="2"></textarea>
                            </div>
                            <div class="col-12 d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Register
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <p class="mb-0">Already have an account?
                                <a href="#" class="text-decoration-none" onclick="event.preventDefault(); showLoginForm(); return false;">
                                    <strong>Login here</strong>
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"
></script>

<script>
    // Auth Modal Functions
    function showLoginForm() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const modalHeader = document.getElementById('authModalHeader');
        const modalLabel = document.getElementById('authModalLabel');

        if (!loginForm || !registerForm || !modalHeader || !modalLabel) return;

        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        modalHeader.className = 'modal-header bg-dark text-white';
        modalLabel.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Login';

        // Clear alerts
        const errorAlert = document.getElementById('authErrorAlert');
        const successAlert = document.getElementById('authSuccessAlert');
        if (errorAlert) errorAlert.classList.add('d-none');
        if (successAlert) successAlert.classList.add('d-none');

        // Focus on username field
        setTimeout(() => {
            const usernameField = document.getElementById('modal_login_username');
            if (usernameField) usernameField.focus();
        }, 300);
    }

    function showRegisterForm() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const modalHeader = document.getElementById('authModalHeader');
        const modalLabel = document.getElementById('authModalLabel');

        if (!loginForm || !registerForm || !modalHeader || !modalLabel) return;

        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        modalHeader.className = 'modal-header bg-primary text-white';
        modalLabel.innerHTML = '<i class="bi bi-person-plus"></i> Create an account';

        // Clear alerts
        const errorAlert = document.getElementById('authErrorAlert');
        const successAlert = document.getElementById('authSuccessAlert');
        if (errorAlert) errorAlert.classList.add('d-none');
        if (successAlert) successAlert.classList.add('d-none');

        // Focus on username field
        setTimeout(() => {
            const usernameField = document.getElementById('modal_reg_username');
            if (usernameField) usernameField.focus();
        }, 300);
    }

    // Check URL parameters on page load
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const success = urlParams.get('success');
        const form = urlParams.get('form'); // 'login' or 'register'

        if (error || success || form) {
            const modalElement = document.getElementById('authModal');
            if (!modalElement) return;

            const modal = new bootstrap.Modal(modalElement);

            if (form === 'register') {
                showRegisterForm();
            } else {
                showLoginForm();
            }

            if (error) {
                const errorAlert = document.getElementById('authErrorAlert');
                const errorText = document.getElementById('authErrorText');
                if (errorAlert && errorText) {
                    errorText.textContent = decodeURIComponent(error);
                    errorAlert.classList.remove('d-none');
                }
            }

            if (success) {
                const successAlert = document.getElementById('authSuccessAlert');
                const successText = document.getElementById('authSuccessText');
                if (successAlert && successText) {
                    successText.textContent = decodeURIComponent(success);
                    successAlert.classList.remove('d-none');
                }
            }

            modal.show();

            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    })();
</script>
</body>
</html>
