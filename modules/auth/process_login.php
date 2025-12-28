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

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($username) || empty($password)) {
    header('Location: index.php?module=public&action=home&error=' . urlencode('Username and password are required') . '&form=login');
    exit;
}

// Query user from database
$stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ?");
if (!$stmt) {
    header('Location: index.php?module=public&action=home&error=' . urlencode('Database error') . '&form=login');
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Verify user exists and password is correct
if ($user && password_verify($password, $user['password'])) {
    // Login successful
    loginUser($user['id'], $user['username'], $user['role']);

    // Redirect back to home page with success message
    header('Location: index.php?module=public&action=home&success=' . urlencode('Login successful! You are now logged in.') . '&form=login');
    exit;
} else {
    // Login failed
    header('Location: index.php?module=public&action=home&error=' . urlencode('Invalid username or password') . '&form=login');
    exit;
}
