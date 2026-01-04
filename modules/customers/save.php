<?php
// Handle create / update customer

require_once __DIR__ . '/../../db.php';

$id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$full_name      = trim($_POST['full_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$address        = trim($_POST['address'] ?? '');

// Validation
if (empty($full_name)) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Full name is required'));
    exit;
}

if (strlen($full_name) > 255) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Full name must be 255 characters or less'));
    exit;
}

if (empty($email)) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Email is required'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Invalid email format'));
    exit;
}

// Check if email already exists (excluding current customer if editing)
if ($id > 0) {
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ? AND id != ?");
    mysqli_stmt_bind_param($checkStmt, 'si', $email, $id);
} else {
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE email = ?");
    mysqli_stmt_bind_param($checkStmt, 's', $email);
}
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
if (mysqli_fetch_assoc($result)) {
    mysqli_stmt_close($checkStmt);
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Email already exists'));
    exit;
}
mysqli_stmt_close($checkStmt);

$success = false;
if ($id > 0) {
    // Verify customer exists
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE id = ?");
    mysqli_stmt_bind_param($checkStmt, 'i', $id);
    mysqli_stmt_execute($checkStmt);
    $result = mysqli_stmt_get_result($checkStmt);
    if (!mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($checkStmt);
        header('Location: index.php?module=customers&action=list&error=' . urlencode('Customer not found'));
        exit;
    }
    mysqli_stmt_close($checkStmt);

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE customers SET full_name = ?, email = ?, contact_number = ?, address = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $full_name, $email, $contact_number, $address, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO customers (full_name, email, contact_number, address, user_id) VALUES (?, ?, ?, ?, 0)"
    );
    mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $email, $contact_number, $address);
    if (mysqli_stmt_execute($stmt)) {
        $success = true;
    }
    mysqli_stmt_close($stmt);
}

if ($success) {
    $message = $id > 0 ? 'Customer updated successfully!' : 'Customer created successfully!';
    header('Location: index.php?module=customers&action=list&success=' . urlencode($message));
} else {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Failed to save customer. Please try again.'));
}
exit;
