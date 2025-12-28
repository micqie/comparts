<?php
// Reports Dashboard with Sales Analytics

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

// Get statistics
$stats = [];

// Total Sales
$result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
$row = mysqli_fetch_assoc($result);
$stats['total_sales'] = $row['total'] ?? 0;

// Total Orders
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
$row = mysqli_fetch_assoc($result);
$stats['total_orders'] = $row['count'] ?? 0;

// Total Customers
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM customers");
$row = mysqli_fetch_assoc($result);
$stats['total_customers'] = $row['count'] ?? 0;

// Total Products
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$row = mysqli_fetch_assoc($result);
$stats['total_products'] = $row['count'] ?? 0;

// Pending Orders
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
$row = mysqli_fetch_assoc($result);
$stats['pending_orders'] = $row['count'] ?? 0;

// Sales by Month (last 6 months)
$salesByMonth = [];
$result = mysqli_query($conn, "
    SELECT
        DATE_FORMAT(order_date, '%Y-%m') as month,
        SUM(total_amount) as total
    FROM orders
    WHERE status = 'completed'
    AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month ASC
");
while ($row = mysqli_fetch_assoc($result)) {
    $salesByMonth[] = [
        'month' => date('M Y', strtotime($row['month'] . '-01')),
        'total' => (float)$row['total']
    ];
}

// Top Selling Products
$topProducts = [];
$result = mysqli_query($conn, "
    SELECT
        p.product_name,
        SUM(od.quantity) as total_sold,
        SUM(od.quantity * od.price) as revenue
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    JOIN orders o ON od.order_id = o.id
    WHERE o.status = 'completed'
    GROUP BY p.id, p.product_name
    ORDER BY total_sold DESC
    LIMIT 5
");
while ($row = mysqli_fetch_assoc($result)) {
    $topProducts[] = $row;
}

// Sales by Status
$salesByStatus = [];
$result = mysqli_query($conn, "
    SELECT
        status,
        COUNT(*) as count,
        SUM(total_amount) as total
    FROM orders
    GROUP BY status
");
while ($row = mysqli_fetch_assoc($result)) {
    $salesByStatus[] = $row;
}
?>
<div class="page-header">
    <h2><i class="bi bi-graph-up"></i> Reports & Analytics</h2>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="value">$<?php echo number_format((float)$stats['total_sales'], 2); ?></div>
            <div class="label">Total Sales</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-cart-check"></i></div>
            <div class="value"><?php echo $stats['total_orders']; ?></div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-people"></i></div>
            <div class="value"><?php echo $stats['total_customers']; ?></div>
            <div class="label">Total Customers</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-box-seam"></i></div>
            <div class="value"><?php echo $stats['total_products']; ?></div>
            <div class="label">Total Products</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-8">
        <div class="chart-container">
            <h5><i class="bi bi-bar-chart"></i> Sales Trend (Last 6 Months)</h5>
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-container">
            <h5><i class="bi bi-pie-chart"></i> Orders by Status</h5>
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-trophy"></i> Top Selling Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Total Sold</th>
                            <th>Revenue</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($topProducts) > 0): ?>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo $product['total_sold']; ?></span></td>
                                    <td><strong>$<?php echo number_format((float)$product['revenue'], 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No sales data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sales Trend Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesData = {
    labels: <?php echo json_encode(array_column($salesByMonth, 'month')); ?>,
    datasets: [{
        label: 'Sales ($)',
        data: <?php echo json_encode(array_column($salesByMonth, 'total')); ?>,
        borderColor: 'rgb(102, 126, 234)',
        backgroundColor: 'rgba(102, 126, 234, 0.1)',
        tension: 0.4,
        fill: true
    }]
};

new Chart(salesCtx, {
    type: 'line',
    data: salesData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            }
        }
    }
});

// Status Pie Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = {
    labels: <?php echo json_encode(array_column($salesByStatus, 'status')); ?>,
    datasets: [{
        data: <?php echo json_encode(array_column($salesByStatus, 'count')); ?>,
        backgroundColor: [
            'rgba(255, 193, 7, 0.8)',
            'rgba(40, 167, 69, 0.8)',
            'rgba(220, 53, 69, 0.8)'
        ],
        borderWidth: 2,
        borderColor: '#fff'
    }]
};

new Chart(statusCtx, {
    type: 'doughnut',
    data: statusData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

