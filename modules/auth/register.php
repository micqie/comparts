<?php
// Customer Registration page
require_once __DIR__ . '/../../config/auth.php';

// If already logged in, redirect based on role
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: index.php?module=products&action=list');
    } else {
        header('Location: index.php?module=customer&action=dashboard');
    }
    exit;
}

$error = '';
$success = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Computer Parts Ordering System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="index.php?module=public&action=home" class="text-white text-decoration-none">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
        <div class="card shadow-lg">
            <div class="card-header">
                <h4 class="mb-0 text-center">
                    <i class="bi bi-person-plus"></i> Customer Registration
                </h4>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?module=auth&action=process_register">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>
                    <div class="text-center">
                        <p class="mb-0">Already have an account?
                            <a href="index.php?module=auth&action=login" class="text-decoration-none">
                                <strong>Login here</strong>
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center mt-3">
            <small class="text-white">Computer Parts Ordering System</small>
        </div>
    </div>
</div>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"
></script>
</body>
</html>
