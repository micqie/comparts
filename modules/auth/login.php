<?php
// Login page
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
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Computer Parts Ordering System</title>
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
            max-width: 450px;
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
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </h4>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?module=auth&action=process_login">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </div>
                    <div class="text-center">
                        <p class="mb-0">Don't have an account?
                            <a href="index.php?module=auth&action=register" class="text-decoration-none">
                                <strong>Register here</strong>
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
