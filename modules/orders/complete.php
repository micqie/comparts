<?php
// Complete order payment (Admin only)

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

// Only admins can complete orders
if (!isAdmin()) {
    header('Location: index.php?module=customer&action=dashboard');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Invalid order ID'));
    exit;
}

// Verify order exists and is pending
$stmt = mysqli_prepare($conn, "SELECT id, status FROM orders WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Order not found'));
    exit;
}

if ($order['status'] !== 'pending') {
    $statusText = ucfirst($order['status']);
    header('Location: index.php?module=orders&action=view&id=' . $id . '&error=' . urlencode("Order is already {$statusText}. Cannot process payment."));
    exit;
}

// Update order status to completed
$new_status = 'completed';
$stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'si', $new_status, $id);
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?module=orders&action=view&id=' . $id . '&success=' . urlencode('Order payment confirmed successfully!'));
} else {
    mysqli_stmt_close($stmt);
    header('Location: index.php?module=orders&action=view&id=' . $id . '&error=' . urlencode('Failed to update order status. Please try again.'));
}
exit;
