<?php
// Save new order with items

require_once __DIR__ . '/../../db.php';

$customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$product_ids = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

// Validation
if ($customer_id <= 0) {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Please select a customer'));
    exit;
}

// Verify customer exists
$checkStmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE id = ?");
mysqli_stmt_bind_param($checkStmt, 'i', $customer_id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
if (!mysqli_fetch_assoc($result)) {
    mysqli_stmt_close($checkStmt);
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Invalid customer selected'));
    exit;
}
mysqli_stmt_close($checkStmt);

if (empty($product_ids) || empty($quantities)) {
    header('Location: index.php?module=orders&action=list&error=' . urlencode('Please add at least one product to the order'));
    exit;
}

// Clean up items
$items = [];
for ($i = 0, $len = count($product_ids); $i < $len; $i++) {
    $pid = (int)$product_ids[$i];
    $qty = (int)$quantities[$i];
    if ($pid > 0 && $qty > 0) {
        $items[] = ['product_id' => $pid, 'qty' => $qty];
    }
}

if (count($items) === 0) {
    header('Location: index.php?module=orders&action=list');
    exit;
}

mysqli_begin_transaction($conn);

try {
    // Insert order with temp total
    $status = 'pending';
    $initialTotal = 0.0;
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ids', $customer_id, $initialTotal, $status);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $grandTotal = 0.0;

    // Insert order items and update stock + inventory transaction
    foreach ($items as $item) {
        $pid = $item['product_id'];
        $qty = $item['qty'];

        // Get product price and stock
        $stmt = mysqli_prepare(
            $conn,
            "SELECT price, stock_quantity FROM products WHERE id = ? FOR UPDATE"
        );
        mysqli_stmt_bind_param($stmt, 'i', $pid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$product) {
            throw new Exception('Product not found: ' . $pid);
        }

        $price = (float)$product['price'];
        $stock = (int)$product['stock_quantity'];

        if ($qty > $stock) {
            throw new Exception('Not enough stock for product ID ' . $pid);
        }

        $lineTotal = $price * $qty;
        $grandTotal += $lineTotal;

        // Insert order_details
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'iiid', $order_id, $pid, $qty, $price);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update product stock
        $newStock = $stock - $qty;
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE products SET stock_quantity = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, 'ii', $newStock, $pid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Record inventory transaction (out)
        $transactionType = 'out';
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO inventory_transactions (product_id, supplier_id, transaction_type, quantity)
             VALUES (?, NULL, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'isi', $pid, $transactionType, $qty);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Update order total
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE orders SET total_amount = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'di', $grandTotal, $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);
    header('Location: index.php?module=orders&action=list&success=' . urlencode("Order #{$order_id} created successfully!"));
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $errorMessage = 'Failed to create order: ' . htmlspecialchars($e->getMessage());
    header('Location: index.php?module=orders&action=list&error=' . urlencode($errorMessage));
    exit;
}
