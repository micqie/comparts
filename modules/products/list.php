<?php
// List all products (computer parts)

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$result = mysqli_query(
    $conn,
    "SELECT p.id, p.product_name, p.price, p.stock_quantity, c.category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.id DESC"
);

// Load categories for modal
$categories = mysqli_query($conn, "SELECT id, category_name FROM categories ORDER BY category_name");
$categoryList = [];
while ($cat = mysqli_fetch_assoc($categories)) {
    $categoryList[] = $cat;
}
?>
<div class="page-header">
    <h2><i class="bi bi-box-seam"></i> Products</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openProductModal(0)">
        <i class="bi bi-plus-circle"></i> Add Product
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
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format((float)$row['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo (int)$row['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo htmlspecialchars($row['stock_quantity']); ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning"
                                        onclick="openProductModal(<?php echo $row['id']; ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="index.php?module=products&action=delete&id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this product?');">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No products found. Add your first product!</p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="index.php?module=products&action=save" id="productForm" data-modal="productModal">
                <div class="modal-body">
                    <input type="hidden" name="id" id="product_id">

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categoryList as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="product_price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openProductModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const form = document.getElementById('productForm');
    const title = document.getElementById('productModalTitle');

    // Reset form
    form.reset();
    document.getElementById('product_id').value = '';

    if (id > 0) {
        // Load product data
        fetch(`index.php?module=products&action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('product_id').value = data.id;
                document.getElementById('product_name').value = data.product_name;
                document.getElementById('category_id').value = data.category_id;
                document.getElementById('product_price').value = data.price;
                document.getElementById('stock_quantity').value = data.stock_quantity;
                title.textContent = 'Edit Product';
            })
            .catch(() => {
                // If get action doesn't exist, we'll load via form page
                window.location.href = `index.php?module=products&action=form&id=${id}`;
            });
    } else {
        title.textContent = 'Add Product';
    }

    modal.show();
}
</script>
