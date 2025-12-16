<?php
// Process customer registration
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../config/auth.php';

// If already logged in, redirect
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
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

// Validation
if (empty($username) || empty($password) || empty($full_name) || empty($email)) {
    header('Location: index.php?module=auth&action=register&error=' . urlencode('All required fields must be filled'));
    exit;
}

if (strlen($password) < 6) {
    header('Location: index.php?module=auth&action=register&error=' . urlencode('Password must be at least 6 characters'));
    exit;
}

if ($password !== $confirm_password) {
    header('Location: index.php?module=auth&action=register&error=' . urlencode('Passwords do not match'));
    exit;
}

// Check if username already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_fetch_assoc($result)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?module=auth&action=register&error=' . urlencode('Username already exists'));
    exit;
}
mysqli_stmt_close($stmt);

// Check if email already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ?");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_fetch_assoc($result)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?module=auth&action=register&error=' . urlencode('Email already registered'));
    exit;
}
mysqli_stmt_close($stmt);

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$role = 'customer';

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert user
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $username, $hashedPassword, $role);
    mysqli_stmt_execute($stmt);
    $user_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Insert customer
    $stmt = mysqli_prepare($conn, "INSERT INTO customers (user_id, full_name, email, contact_number, address) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'issss', $user_id, $full_name, $email, $contact_number, $address);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Commit transaction
    mysqli_commit($conn);

    // Auto-login the user
    loginUser($user_id, $username, $role);

    header('Location: index.php?module=customer&action=dashboard');
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    header('Location: index.php?module=auth&action=register&error=' . urlencode('Registration failed. Please try again.'));
    exit;
}
