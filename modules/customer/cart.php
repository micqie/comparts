<?php
// Shopping Cart

if (!isset($conn)) {
    require_once __DIR__ . '/../../db.php';
}

require_once __DIR__ . '/../../config/auth.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$total = 0;

// Calculate totals
foreach ($cart as &$item) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
}
?>
<div class="page-header">
    <h2><i class="bi bi-cart"></i> Shopping Cart</h2>
</div>

<?php if (empty($cart)): ?>
    <div class="card">
        <div class="card-body text-center empty-state">
            <i class="bi bi-cart-x"></i>
            <h4>Your cart is empty</h4>
            <p>Start adding products to your cart!</p>
            <a href="index.php?module=customer&action=products" class="btn btn-primary">
                <i class="bi bi-box-seam"></i> Browse Products
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart as $index => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="post" action="index.php?module=customer&action=update_cart" class="d-inline">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <div class="input-group" style="width: 120px;">
                                        <input type="number" name="quantity" class="form-control form-control-sm"
                                               value="<?php echo $item['quantity']; ?>" min="1" required>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                            <td>
                                <form method="post" action="index.php?module=customer&action=remove_from_cart" class="d-inline">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong style="font-size: 1.3rem; color: var(--primary-color);">
                            $<?php echo number_format($total, 2); ?>
                        </strong></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="index.php?module=customer&action=products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Continue Shopping
            </a>
            <a href="index.php?module=customer&action=checkout" class="btn btn-primary float-end">
                <i class="bi bi-check-circle"></i> Proceed to Checkout
            </a>
        </div>
    </div>
<?php endif; ?>

