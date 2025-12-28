<?php
// Handle create / update customer

require_once __DIR__ . '/../../db.php';

$id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$full_name      = trim($_POST['full_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$address        = trim($_POST['address'] ?? '');

if ($full_name === '' || $email === '') {
    header('Location: index.php?module=customers&action=list');
    exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE customers SET full_name = ?, email = ?, contact_number = ?, address = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $full_name, $email, $contact_number, $address, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO customers (full_name, email, contact_number, address, user_id) VALUES (?, ?, ?, ?, 0)"
    );
    mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $email, $contact_number, $address);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header('Location: index.php?module=customers&action=list');
exit;



