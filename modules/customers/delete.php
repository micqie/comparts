<?php
// Delete customer

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Invalid customer ID'));
    exit;
}

// Check if customer exists and get name
$checkStmt = mysqli_prepare($conn, "SELECT full_name FROM customers WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if (!$customer) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Customer not found'));
    exit;
}

// Check if customer has any orders
$checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE customer_id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if ($row['count'] > 0) {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Cannot delete customer: Customer has ' . $row['count'] . ' order(s).'));
    exit;
}

// Safe to delete
$stmt = mysqli_prepare($conn, "DELETE FROM customers WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    $customerName = htmlspecialchars($customer['full_name']);
    header('Location: index.php?module=customers&action=list&success=' . urlencode("Customer '{$customerName}' deleted successfully!"));
} else {
    header('Location: index.php?module=customers&action=list&error=' . urlencode('Failed to delete customer. Please try again.'));
}
mysqli_stmt_close($stmt);
exit;
