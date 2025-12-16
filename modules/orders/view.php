<?php
// View single order details

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if customer can view this order (if not admin)
if (!isAdmin()) {
    $user_id = getUserId();
    $stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $customer = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($customer) {
        // Verify order belongs to this customer
        $stmt = mysqli_prepare($conn, "SELECT customer_id FROM orders WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orderCheck = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$orderCheck || $orderCheck['customer_id'] != $customer['id']) {
            header('Location: index.php?module=customer&action=orders');
            exit;
        }
    }
}

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

if (!$order) : ?>
    <div class="page-header">
        <h2>Order Not Found</h2>
    </div>
    <div class="alert alert-danger">Order not found.</div>
<?php
    return;
endif;

$details = mysqli_query(
    $conn,
    "SELECT od.quantity, od.price, p.product_name
     FROM order_details od
     JOIN products p ON od.product_id = p.id
     WHERE od.order_id = " . (int)$order['id']
);
?>
<div class="page-header">
    <h2><i class="bi bi-receipt"></i> Order #<?php echo htmlspecialchars($order['id']); ?></h2>
    <a href="index.php?module=orders&action=list" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                <p class="mb-2"><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact_number'] ?? 'N/A'); ?></p>
                <p class="mb-0"><strong>Address:</strong> <?php echo htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Order Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></p>
                <p class="mb-2">
                    <strong>Status:</strong>
                    <?php
                    $statusClass = [
                        'pending' => 'bg-warning',
                        'completed' => 'bg-success',
                        'cancelled' => 'bg-danger'
                    ];
                    $class = $statusClass[$order['status']] ?? 'bg-secondary';
                    ?>
                    <span class="badge <?php echo $class; ?>">
                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                    </span>
                </p>
                <p class="mb-0"><strong>Total:</strong> <span style="font-size: 1.5rem; color: var(--primary-color);">$<?php echo number_format((float)$order['total_amount'], 2); ?></span></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Order Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Line Total</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($details)) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>$<?php echo number_format((float)$row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><strong>$<?php echo number_format((float)$row['price'] * (int)$row['quantity'], 2); ?></strong></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
