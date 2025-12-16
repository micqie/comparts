<?php
// Customer Orders

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

$user_id = getUserId();

// Get customer ID
$stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$customer) {
    header('Location: index.php?module=auth&action=logout');
    exit;
}

$customer_id = $customer['id'];

// Get orders
$orders = [];
$stmt = mysqli_prepare($conn, "
    SELECT id, order_date, total_amount, status
    FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
");
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt);
?>
<div class="page-header">
    <h2><i class="bi bi-receipt"></i> My Orders</h2>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (count($orders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></td>
                            <td><strong>$<?php echo number_format((float)$order['total_amount'], 2); ?></strong></td>
                            <td>
                                <?php
                                $statusClass = [
                                    'pending' => 'bg-warning',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ];
                                $class = $statusClass[$order['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?php echo $class; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?module=orders&action=view&id=<?php echo $order['id']; ?>"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>You haven't placed any orders yet.</p>
                <a href="index.php?module=customer&action=products" class="btn btn-primary">
                    <i class="bi bi-box-seam"></i> Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
