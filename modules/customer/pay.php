<?php
// Process payment for an order

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

header('Content-Type: application/json');

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

$user_id = getUserId();

// Get customer ID
$stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$customer) {
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    exit;
}

$customer_id = $customer['id'];

// Verify order belongs to this customer and is pending
$stmt = mysqli_prepare($conn, "SELECT id, status FROM orders WHERE id = ? AND customer_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $order_id, $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found or access denied']);
    exit;
}

if ($order['status'] !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Order is not pending. Current status: ' . $order['status']]);
    exit;
}

// Update order status to completed
$new_status = 'completed';
$stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'si', $new_status, $order_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'Payment processed successfully!']);
} else {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Failed to process payment']);
}
