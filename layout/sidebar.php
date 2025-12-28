<?php
// Sidebar navigation
require_once __DIR__ . '/../config/auth.php';

$currentModule = isset($_GET['module']) ? $_GET['module'] : 'products';
?>
<button class="mobile-menu-btn" onclick="document.querySelector('.sidebar').classList.toggle('show')">
    <i class="bi bi-list"></i>
</button>

<aside class="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-cpu"></i> Computer Parts</h4>
        <span class="badge bg-primary">Admin Panel</span>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="index.php?module=categories&action=list"
               class="<?php echo $currentModule === 'categories' ? 'active' : ''; ?>">
                <i class="bi bi-tags"></i>
                <span>Categories</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=products&action=list"
               class="<?php echo $currentModule === 'products' ? 'active' : ''; ?>">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=customers&action=list"
               class="<?php echo $currentModule === 'customers' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Customers</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=orders&action=list"
               class="<?php echo $currentModule === 'orders' ? 'active' : ''; ?>">
                <i class="bi bi-cart-check"></i>
                <span>Orders</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=reports&action=dashboard"
               class="<?php echo $currentModule === 'reports' ? 'active' : ''; ?>">
                <i class="bi bi-graph-up"></i>
                <span>Reports & Analytics</span>
            </a>
        </li>
        <li>
            <a href="index.php?module=reports&action=sales"
               class="<?php echo ($currentModule === 'reports' && isset($_GET['action']) && $_GET['action'] === 'sales') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Sales Report</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <i class="bi bi-person-circle"></i>
            <div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars(getUsername()); ?></div>
                <small style="opacity: 0.8;"><?php echo htmlspecialchars(getUserRole()); ?></small>
            </div>
        </div>
        <a href="index.php?module=auth&action=logout" class="btn btn-logout">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</aside>
