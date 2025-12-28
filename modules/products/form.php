<?php
// Create / edit product form

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = [
    'product_name' => '',
    'category_id'  => '',
    'price'        => '',
    'stock_quantity' => '',
];

// Load categories
$categories = mysqli_query($conn, "SELECT id, category_name FROM categories ORDER BY category_name");

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT id, product_name, category_id, price, stock_quantity FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>
<div class="container mt-4">
    <h2><?php echo $id > 0 ? 'Edit Product' : 'Add Product'; ?></h2>

    <form method="post" action="index.php?module=products&action=save">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$id); ?>">

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="product_name" class="form-control" required
                   value="<?php echo htmlspecialchars((string)($product['product_name'] ?? '')); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?php echo $cat['id']; ?>"
                        <?php echo (string)$cat['id'] === (string)($product['category_id'] ?? '') ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required
                   value="<?php echo htmlspecialchars((string)($product['price'] ?? '')); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" required
                   value="<?php echo htmlspecialchars((string)($product['stock_quantity'] ?? '')); ?>">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="index.php?module=products&action=list" class="btn btn-secondary">Cancel</a>
    </form>
</div>



