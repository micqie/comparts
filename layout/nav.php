<?php
// Top navigation links
require_once __DIR__ . '/../config/auth.php';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php?module=products&action=list">
            <i class="bi bi-cpu"></i> Computer Parts
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?module=products&action=list">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?module=customers&action=list">Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?module=orders&action=list">Orders</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars(getUsername()); ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars(getUserRole()); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="index.php?module=auth&action=logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?module=auth&action=login">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
