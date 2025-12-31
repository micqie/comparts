<?php
// Process login form submission
require_once __DIR__ . '/../../db.php';
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

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    header('Location: index.php?module=public&action=home&error=' . urlencode('Email and password are required') . '&form=login');
    exit;
}

// Query user from database by email
// Check both: email in customers table (for customers) OR username in users table (for admin)
$stmt = mysqli_prepare($conn, "SELECT DISTINCT u.id, u.username, u.password, u.role FROM users u LEFT JOIN customers c ON u.id = c.user_id WHERE c.email = ? OR (u.role = 'admin' AND u.username = ?)");
if (!$stmt) {
    header('Location: index.php?module=public&action=home&error=' . urlencode('Database error') . '&form=login');
    exit;
}

mysqli_stmt_bind_param($stmt, 'ss', $email, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Verify user exists and password is correct
if ($user && password_verify($password, $user['password'])) {
    // Login successful
    loginUser($user['id'], $user['username'], $user['role']);

    // Redirect directly to dashboard based on role
    if ($user['role'] === 'admin') {
        header('Location: index.php?module=products&action=list');
    } else {
        header('Location: index.php?module=customer&action=dashboard');
    }
    exit;
} else {
    // Login failed
    header('Location: index.php?module=public&action=home&error=' . urlencode('Invalid email or password') . '&form=login');
    exit;
}
