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
                                <button type="button" class="btn btn-sm btn-info"
                                        onclick="viewOrderDetails(<?php echo $order['id']; ?>)"
                                        data-order-id="<?php echo $order['id']; ?>">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
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

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">
                    <i class="bi bi-receipt"></i> Order Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading order details...</p>
                </div>
            </div>
            <div class="modal-footer" id="orderDetailsFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();

    // Reset content to loading state
    const content = document.getElementById('orderDetailsContent');
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading order details...</p>
        </div>
    `;

    // Fetch order details
    fetch(`index.php?module=orders&action=get&id=${orderId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            if (data.error) {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> ${data.error}
                    </div>
                `;
                return;
            }

            // Format order date
            const orderDate = new Date(data.order_date);
            const formattedDate = orderDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            // Status badge
            const statusClass = {
                'pending': 'bg-warning',
                'completed': 'bg-success',
                'cancelled': 'bg-danger'
            };
            const statusBadgeClass = statusClass[data.status] || 'bg-secondary';
            const statusText = data.status.charAt(0).toUpperCase() + data.status.slice(1);

            // Build items table
            let itemsTable = '';
            if (data.items && data.items.length > 0) {
                itemsTable = `
                    <div class="table-responsive mt-3">
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
                                ${data.items.map(item => `
                                    <tr>
                                        <td>${escapeHtml(item.product_name)}</td>
                                        <td>$${parseFloat(item.price).toFixed(2)}</td>
                                        <td>${escapeHtml(item.quantity)}</td>
                                        <td><strong>$${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</strong></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                itemsTable = '<p class="text-muted">No items found.</p>';
            }

            // Store order data globally for payment function
            window.currentOrderData = data;

            // Build the content
            content.innerHTML = `
                <div class="mb-3">
                    <h5 class="mb-3"><i class="bi bi-receipt"></i> Order #${data.id}</h5>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-person"></i> Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Name:</strong> ${escapeHtml(data.full_name)}</p>
                                <p class="mb-2"><strong>Email:</strong> ${escapeHtml(data.email)}</p>
                                <p class="mb-2"><strong>Contact:</strong> ${escapeHtml(data.contact_number || 'N/A')}</p>
                                <p class="mb-0"><strong>Address:</strong> ${escapeHtml(data.address || 'N/A')}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Order Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Date:</strong> ${formattedDate}</p>
                                <p class="mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge ${statusBadgeClass} ms-2" id="orderStatusBadge">${statusText}</span>
                                </p>
                                <p class="mb-0">
                                    <strong>Total:</strong>
                                    <span class="fs-4 text-primary">$${parseFloat(data.total_amount).toFixed(2)}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-list-ul"></i> Order Items</h6>
                    </div>
                    <div class="card-body">
                        ${itemsTable}
                    </div>
                </div>
            `;

            // Update modal footer with payment button if pending
            const footer = document.getElementById('orderDetailsFooter');
            if (data.status === 'pending') {
                footer.innerHTML = `
                    <button type="button" class="btn btn-success" onclick="processPayment(${data.id})" id="payButton">
                        <i class="bi bi-credit-card"></i> Pay Now
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                `;
            } else {
                footer.innerHTML = `
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> An error occurred while loading order details. Please try again.
                </div>
            `;
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function processPayment(orderId) {
    if (!confirm('Are you sure you want to proceed with the payment?')) {
        return;
    }

    const payButton = document.getElementById('payButton');
    const originalText = payButton.innerHTML;
    payButton.disabled = true;
    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    // Create form data
    const formData = new FormData();
    formData.append('order_id', orderId);

    // Send payment request
    fetch('index.php?module=customer&action=pay', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badge
            const statusBadge = document.getElementById('orderStatusBadge');
            if (statusBadge) {
                statusBadge.textContent = 'Completed';
                statusBadge.className = 'badge bg-success ms-2';
            }

            // Update footer to remove payment button
            const footer = document.getElementById('orderDetailsFooter');
            footer.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            `;

            // Update global order data
            if (window.currentOrderData) {
                window.currentOrderData.status = 'completed';
            }

            // Show success message
            const content = document.getElementById('orderDetailsContent');
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            content.insertBefore(alertDiv, content.firstChild);

            // Reload the page after 2 seconds to update the order list
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            alert('Payment failed: ' + (data.message || 'Unknown error'));
            payButton.disabled = false;
            payButton.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing payment. Please try again.');
        payButton.disabled = false;
        payButton.innerHTML = originalText;
    });
}
</script>
