<?php
// Get order data as JSON for modal
// Start output buffering to catch any accidental output
ob_start();

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

// Clear any output that might have been generated before headers
ob_end_clean();

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Check if customer can view this order (if not admin)
    if (!isAdmin()) {
        $user_id = getUserId();
        $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $customer = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$customer) {
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        // Verify order belongs to this customer
        $stmt = mysqli_prepare($conn, "SELECT customer_id FROM orders WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orderCheck = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$orderCheck || $orderCheck['customer_id'] != $customer['id']) {
            echo json_encode(['error' => 'Order not found']);
            exit;
        }
    }

    // Get order information
    $stmt = mysqli_prepare(
        $conn,
        "SELECT o.id, o.order_date, o.total_amount, o.status,
                c.full_name, c.email, c.contact_number, c.address
         FROM orders o
         JOIN customers c ON o.customer_id = c.id
         WHERE o.id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $orderResult = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($orderResult);
    mysqli_stmt_close($stmt);

    if ($order) {
        // Get order details
        $stmt = mysqli_prepare(
            $conn,
            "SELECT od.quantity, od.price, p.product_name
             FROM order_details od
             JOIN products p ON od.product_id = p.id
             WHERE od.order_id = ?"
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $detailsResult = mysqli_stmt_get_result($stmt);

        $items = [];
        while ($row = mysqli_fetch_assoc($detailsResult)) {
            $items[] = $row;
        }
        mysqli_stmt_close($stmt);

        $order['items'] = $items;
        echo json_encode($order);
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'Order not found']);
exit;
