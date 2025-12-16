<?php
// Get customer data as JSON for modal
header('Content-Type: application/json');

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, full_name, email, contact_number, address FROM customers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $customer = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($customer) {
        echo json_encode($customer);
        exit;
    }
}

echo json_encode(['error' => 'Customer not found']);
