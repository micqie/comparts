<?php
// Customer navigation sidebar
require_once __DIR__ . '/../config/auth.php';

$currentModule = isset($_GET['module']) ? $_GET['module'] : 'customer';
$currentAction = isset($_GET['action']) ? $_GET['action'] : 'dashboard';
?>
<button class="mobile-menu-btn" onclick="document.querySelector('.sidebar').classList.toggle('show')">
    <i class="bi bi-list"></i>
</button>

<aside class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-cpu"></i> Computer Parts</h4>
        <span class="badge bg-success">Customer</span>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="index.php?module=customer&action=dashboard"
               class="<?php echo ($currentModule === 'customer' && $currentAction === 'dashboard') ? 'active' : ''; ?>">
                <i class="bi bi-house"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=customer&action=products"
               class="<?php echo ($currentModule === 'customer' && $currentAction === 'products') ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=customer&action=cart"
               class="<?php echo ($currentModule === 'customer' && $currentAction === 'cart') ? 'active' : ''; ?>">
                <i class="bi bi-cart"></i>
                <span>Shopping Cart</span>
                <?php
                $cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                if ($cartCount > 0):
                ?>
                    <span class="badge bg-danger ms-2"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="index.php?module=customer&action=orders"
               class="<?php echo ($currentModule === 'customer' && $currentAction === 'orders') ? 'active' : ''; ?>">
                <i class="bi bi-receipt"></i>
                <span>My Orders</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <i class="bi bi-person-circle"></i>
            <div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars(getUsername()); ?></div>
                <small style="opacity: 0.8;">Customer</small>
            </div>
        </div>
        <a href="index.php?module=auth&action=logout" class="btn btn-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</aside>
