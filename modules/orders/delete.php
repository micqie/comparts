<?php
// Delete order (does not restore stock quantities)

require_once __DIR__ . '/../../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Invalid order ID'));
    exit;
}

// Check if order exists
$checkStmt = mysqli_prepare($conn, "SELECT id, total_amount FROM orders WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if (!$order) {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Order not found'));
    exit;
}

// Delete order details first because of FK constraint
$stmt = mysqli_prepare($conn, "DELETE FROM order_details WHERE order_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Delete order
$stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    header('Location: index.php?module=orders&action=list&success=' . urlencode("Order #{$id} deleted successfully!"));
} else {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Failed to delete order. Please try again.'));
}
mysqli_stmt_close($stmt);
exit;
