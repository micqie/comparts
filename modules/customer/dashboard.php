<?php
// Customer Dashboard

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

$user_id = getUserId();

// Get customer info
$stmt = mysqli_prepare($conn, "SELECT id, full_name, email FROM customers WHERE user_id = ?");
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

// Get customer stats
$stats = [];

// Total Orders
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE customer_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$stats['total_orders'] = $row['count'] ?? 0;
mysqli_stmt_close($stmt);

// Total Spent
$stmt = mysqli_prepare($conn, "SELECT SUM(total_amount) as total FROM orders WHERE customer_id = ? AND status = 'completed'");
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$stats['total_spent'] = $row['total'] ?? 0;
mysqli_stmt_close($stmt);

// Pending Orders
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE customer_id = ? AND status = 'pending'");
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$stats['pending_orders'] = $row['count'] ?? 0;
mysqli_stmt_close($stmt);

// Recent Orders
$recentOrders = [];
$stmt = mysqli_prepare($conn, "
    SELECT id, order_date, total_amount, status
    FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
    LIMIT 5
");
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $recentOrders[] = $row;
}
mysqli_stmt_close($stmt);
?>
<div class="page-header">
    <h2><i class="bi bi-house"></i> Welcome, <?php echo htmlspecialchars($customer['full_name']); ?>!</h2>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-cart-check"></i></div>
            <div class="value"><?php echo $stats['total_orders']; ?></div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="value">$<?php echo number_format((float)$stats['total_spent'], 2); ?></div>
            <div class="label">Total Spent</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-clock-history"></i></div>
            <div class="value"><?php echo $stats['pending_orders']; ?></div>
            <div class="label">Pending Orders</div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-receipt"></i> Recent Orders</h5>
    </div>
    <div class="card-body">
        <?php if (count($recentOrders) > 0): ?>
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
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
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
                                    <i class="bi bi-eye"></i> View
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
                <p>No orders yet. Start shopping!</p>
                <a href="index.php?module=customer&action=products" class="btn btn-primary">
                    <i class="bi bi-box-seam"></i> Browse Products
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

