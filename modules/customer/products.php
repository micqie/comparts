<?php
// Customer Products Browsing

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Build query
$where = "p.stock_quantity > 0";
$params = [];
$types = '';

if ($search) {
    $where .= " AND p.product_name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if ($category_id > 0) {
    $where .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

$query = "SELECT p.id, p.product_name, p.price, p.stock_quantity, c.category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE $where
          ORDER BY p.product_name";

$result = mysqli_query($conn, $query);

// Get categories for filter
$categories = mysqli_query($conn, "SELECT id, category_name FROM categories ORDER BY category_name");
?>
<div class="page-header">
    <h2><i class="bi bi-box-seam"></i> Browse Products</h2>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="index.php" class="row g-3">
            <input type="hidden" name="module" value="customer">
            <input type="hidden" name="action" value="products">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search products..."
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select name="category_id" class="form-select">
                    <option value="0">All Categories</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>"
                                <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Grid -->
<div class="row">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                        <p class="text-muted mb-2">
                            <small><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></small>
                        </p>
                        <p class="card-text">
                            <strong class="text-primary" style="font-size: 1.5rem;">
                                $<?php echo number_format((float)$product['price'], 2); ?>
                            </strong>
                        </p>
                        <p class="mb-3">
                            <span class="badge <?php echo (int)$product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                Stock: <?php echo $product['stock_quantity']; ?>
                            </span>
                        </p>
                        <?php if ((int)$product['stock_quantity'] > 0): ?>
                            <form method="post" action="index.php?module=customer&action=add_to_cart" class="d-inline">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="input-group mb-2">
                                    <input type="number" name="quantity" class="form-control" value="1" min="1"
                                           max="<?php echo $product['stock_quantity']; ?>" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No products found matching your criteria.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

