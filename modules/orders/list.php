<?php
// List all orders

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$result = mysqli_query(
    $conn,
    "SELECT o.id, o.order_date, o.total_amount, o.status, c.full_name
     FROM orders o
     JOIN customers c ON o.customer_id = c.id
     ORDER BY o.id DESC"
);
?>
<div class="page-header">
    <h2><i class="bi bi-cart-check"></i> Orders</h2>
    <a href="index.php?module=orders&action=form" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Order
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['order_date'])); ?></td>
                            <td><strong>$<?php echo number_format((float)$row['total_amount'], 2); ?></strong></td>
                            <td>
                                <?php
                                $statusClass = [
                                    'pending' => 'bg-warning',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ];
                                $class = $statusClass[$row['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?php echo $class; ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?module=orders&action=view&id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="index.php?module=orders&action=delete&id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this order? This will not restock items.');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No orders found. Create your first order!</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
