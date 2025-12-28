<?php
// Checkout page

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php?module=customer&action=cart');
    exit;
}

$user_id = getUserId();

// Get customer info
$stmt = mysqli_prepare($conn, "SELECT id, full_name, email, contact_number, address FROM customers WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$customer) {
    header('Location: index.php?module=auth&action=logout');
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;

// Calculate totals and verify stock
$items = [];
foreach ($cart as $item) {
    $stmt = mysqli_prepare($conn, "SELECT id, product_name, price, stock_quantity FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $item['product_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$product || (int)$product['stock_quantity'] < $item['quantity']) {
        header('Location: index.php?module=customer&action=cart&error=' . urlencode('Some items are out of stock'));
        exit;
    }

    $items[] = [
        'product_id' => $product['id'],
        'product_name' => $product['product_name'],
        'price' => (float)$product['price'],
        'quantity' => $item['quantity'],
        'subtotal' => (float)$product['price'] * $item['quantity']
    ];
    $total += $items[count($items) - 1]['subtotal'];
}

if (isset($_POST['confirm_order'])) {
    // Create order
    mysqli_begin_transaction($conn);

    try {
        // Insert order
        $status = 'pending';
        $stmt = mysqli_prepare($conn, "INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ids', $customer['id'], $total, $status);
        mysqli_stmt_execute($stmt);
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        // Insert order details and update stock
        foreach ($items as $item) {
            // Insert order detail
            $stmt = mysqli_prepare($conn, "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Update product stock
            $stmt = mysqli_prepare($conn, "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'ii', $item['quantity'], $item['product_id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Insert inventory transaction
            $transaction_type = 'out';
            $stmt = mysqli_prepare($conn, "INSERT INTO inventory_transactions (product_id, transaction_type, quantity) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'isi', $item['product_id'], $transaction_type, $item['quantity']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_commit($conn);

        // Clear cart
        $_SESSION['cart'] = [];

        header('Location: index.php?module=customer&action=orders&success=' . urlencode('Order placed successfully! Order ID: #' . $order_id));
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header('Location: index.php?module=customer&action=checkout&error=' . urlencode('Order failed. Please try again.'));
        exit;
    }
}
?>
<div class="page-header">
    <h2><i class="bi bi-check-circle"></i> Checkout</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong style="font-size: 1.3rem; color: var(--primary-color);">
                                $<?php echo number_format($total, 2); ?>
                            </strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Delivery Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong><br><?php echo htmlspecialchars($customer['full_name']); ?></p>
                <p><strong>Email:</strong><br><?php echo htmlspecialchars($customer['email']); ?></p>
                <p><strong>Contact:</strong><br><?php echo htmlspecialchars($customer['contact_number'] ?? 'N/A'); ?></p>
                <p><strong>Address:</strong><br><?php echo htmlspecialchars($customer['address'] ?? 'N/A'); ?></p>
            </div>
            <div class="card-footer">
                <form method="post">
                    <button type="submit" name="confirm_order" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Confirm Order
                    </button>
                </form>
                <a href="index.php?module=customer&action=cart" class="btn btn-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left"></i> Back to Cart
                </a>
            </div>
        </div>
    </div>
</div>

