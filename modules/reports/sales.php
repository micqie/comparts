<?php
// Detailed Sales Report

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

// Get date range from query params
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Sales Summary
$result = mysqli_query($conn, "
    SELECT
        COUNT(*) as order_count,
        SUM(total_amount) as total_sales,
        AVG(total_amount) as avg_order_value
    FROM orders
    WHERE order_date BETWEEN '$startDate' AND '$endDate'
    AND status = 'completed'
");
$summary = mysqli_fetch_assoc($result);

// Daily Sales
$dailySales = [];
$result = mysqli_query($conn, "
    SELECT
        DATE(order_date) as date,
        COUNT(*) as order_count,
        SUM(total_amount) as total
    FROM orders
    WHERE order_date BETWEEN '$startDate' AND '$endDate'
    AND status = 'completed'
    GROUP BY DATE(order_date)
    ORDER BY date ASC
");
while ($row = mysqli_fetch_assoc($result)) {
    $dailySales[] = $row;
}

// Product Sales
$productSales = [];
$result = mysqli_query($conn, "
    SELECT
        p.product_name,
        SUM(od.quantity) as total_sold,
        SUM(od.quantity * od.price) as revenue
    FROM order_details od
    JOIN products p ON od.product_id = p.id
    JOIN orders o ON od.order_id = o.id
    WHERE o.order_date BETWEEN '$startDate' AND '$endDate'
    AND o.status = 'completed'
    GROUP BY p.id, p.product_name
    ORDER BY revenue DESC
");
while ($row = mysqli_fetch_assoc($result)) {
    $productSales[] = $row;
}
?>
<div class="page-header">
    <h2><i class="bi bi-file-earmark-text"></i> Sales Report</h2>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-3">
            <input type="hidden" name="module" value="reports">
            <input type="hidden" name="action" value="sales">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-cart-check"></i></div>
            <div class="value"><?php echo $summary['order_count'] ?? 0; ?></div>
            <div class="label">Total Orders</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="value">$<?php echo number_format((float)($summary['total_sales'] ?? 0), 2); ?></div>
            <div class="label">Total Sales</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="value">$<?php echo number_format((float)($summary['avg_order_value'] ?? 0), 2); ?></div>
            <div class="label">Average Order Value</div>
        </div>
    </div>
</div>

<!-- Daily Sales Chart -->
<div class="row">
    <div class="col-md-12">
        <div class="chart-container">
            <h5><i class="bi bi-bar-chart-line"></i> Daily Sales</h5>
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>
</div>

<!-- Product Sales Table -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Product Sales</h5>
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
                        <?php if (count($productSales) > 0): ?>
                            <?php foreach ($productSales as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo $product['total_sold']; ?></span></td>
                                    <td><strong>$<?php echo number_format((float)$product['revenue'], 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No sales data for selected period</td>
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
// Daily Sales Chart
const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
const dailyData = {
    labels: <?php echo json_encode(array_map(function($item) { return date('M d', strtotime($item['date'])); }, $dailySales)); ?>,
    datasets: [{
        label: 'Sales ($)',
        data: <?php echo json_encode(array_column($dailySales, 'total')); ?>,
        backgroundColor: 'rgba(102, 126, 234, 0.6)',
        borderColor: 'rgb(102, 126, 234)',
        borderWidth: 2
    }]
};

new Chart(dailyCtx, {
    type: 'bar',
    data: dailyData,
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
</script>
