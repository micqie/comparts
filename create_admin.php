<?php
/**
 * Script to create a default admin user
 * Run this once to create an admin account
 *
 * Usage: Open in browser: http://localhost/comparts/create_admin.php
 * Or run from command line: php create_admin.php
 */

require_once __DIR__ . '/db.php';

// Default admin credentials (change these after first login!)
$username = 'admin@gmail.com';
$password = 'admin123'; // Change this!
$role = 'admin';

// Check if admin already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($existing) {
    echo "Admin user '$username' already exists!\n";
    echo "If you want to reset the password, delete the user first from the database.\n";
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert admin user
$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $username, $hashedPassword, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin user created successfully!\n";
    echo "Username: $username\n";
    echo "Password: $password\n";
    echo "\n";
    echo "⚠️  IMPORTANT: Change the password after first login!\n";
    echo "⚠️  Delete this file (create_admin.php) after use for security!\n";
} else {
    echo "Error creating admin user: " . mysqli_error($conn) . "\n";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
