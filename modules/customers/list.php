<?php
// List all customers

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$result = mysqli_query(
    $conn,
    "SELECT id, full_name, email, contact_number, address
     FROM customers
     ORDER BY id DESC"
);
?>
<div class="page-header">
    <h2><i class="bi bi-people"></i> Customers</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal" onclick="openCustomerModal(0)">
        <i class="bi bi-plus-circle"></i> Add Customer
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning"
                                        onclick="openCustomerModal(<?php echo $row['id']; ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="index.php?module=customers&action=delete&id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this customer?');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No customers found. Add your first customer!</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalTitle">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="index.php?module=customers&action=save" id="customerForm" data-modal="customerModal">
                <div class="modal-body">
                    <input type="hidden" name="id" id="customer_id">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCustomerModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    const form = document.getElementById('customerForm');
    const title = document.getElementById('customerModalTitle');

    form.reset();
    document.getElementById('customer_id').value = '';

    if (id > 0) {
        fetch(`index.php?module=customers&action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('customer_id').value = data.id;
                document.getElementById('full_name').value = data.full_name;
                document.getElementById('email').value = data.email;
                document.getElementById('contact_number').value = data.contact_number || '';
                document.getElementById('address').value = data.address || '';
                title.textContent = 'Edit Customer';
            })
            .catch(() => {
                window.location.href = `index.php?module=customers&action=form&id=${id}`;
            });
    } else {
        title.textContent = 'Add Customer';
    }

    modal.show();
}
</script>
