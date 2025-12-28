<?php
// Customer Registration page - redirect to home with modal
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

// Redirect to home page with register modal
$error = isset($_GET['error']) ? '&error=' . urlencode($_GET['error']) : '';
$success = isset($_GET['success']) ? '&success=' . urlencode($_GET['success']) : '';
header('Location: index.php?module=public&action=home&form=register' . $error . $success);
exit;
