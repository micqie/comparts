<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config/auth.php';

// Simple routing
$module = isset($_GET['module']) ? $_GET['module'] : 'public';
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Whitelisted modules and actions
$allowedModules = [
    'public'    => ['home', 'about', 'contact'],
    'auth'      => ['login', 'process_login', 'logout', 'register', 'process_register'],
    'products'  => ['list', 'form', 'save', 'delete', 'get'],
    'customers' => ['list', 'form', 'save', 'delete', 'get'],
    'orders'    => ['list', 'form', 'save', 'view', 'delete'],
    'reports'   => ['dashboard', 'sales'],
    'customer'  => ['dashboard', 'products', 'cart', 'checkout', 'orders', 'add_to_cart', 'remove_from_cart', 'update_cart'],
];

// Allow auth and public modules without login check
$publicModules = ['auth', 'public'];

// Check authentication for protected modules
if (!in_array($module, $publicModules, true)) {
    requireLogin();

    // Admin-only modules
    $adminModules = ['products', 'customers', 'orders', 'reports'];
    if (in_array($module, $adminModules, true) && !isAdmin()) {
        header('Location: index.php?module=customer&action=dashboard');
        exit;
    }

    // Customer-only modules
    $customerModules = ['customer'];
    if (in_array($module, $customerModules, true) && isAdmin()) {
        header('Location: index.php?module=products&action=list');
        exit;
    }
}

if (!isset($allowedModules[$module]) || !in_array($action, $allowedModules[$module], true)) {
    // If not logged in, redirect to login, otherwise to products
    if (!isLoggedIn()) {
        $module = 'auth';
        $action = 'login';
    } else {
        $module = 'products';
        $action = 'list';
    }
}

// Common layout (skip for auth pages to avoid showing nav)
$skipLayout = ($module === 'auth' && in_array($action, ['login', 'register']));

if (!$skipLayout) {
    // Public pages use public header/nav
    if ($module === 'public') {
        include __DIR__ . '/layout/public_header.php';
    } else {
        include __DIR__ . '/layout/header.php';
        // Use appropriate navigation based on user role
        if (isAdmin()) {
            include __DIR__ . '/layout/sidebar.php';
        } else {
            include __DIR__ . '/layout/customer_nav.php';
        }
    }
}

// Dispatch to module
$moduleFile = __DIR__ . "/modules/{$module}/{$action}.php";
$wrapClass = ($module === 'public') ? '' : 'main-content';
if (file_exists($moduleFile)) {
    if ($wrapClass) {
        echo "<div class=\"{$wrapClass}\">";
    }
    include $moduleFile;
    if ($wrapClass) {
        echo '</div>';
    }
} else {
    if (!$skipLayout) {
        if ($wrapClass) {
            echo "<div class=\"{$wrapClass}\">";
        }
        echo "<div class=\"page-header\"><h2>Page Not Found</h2></div>";
        echo "<div class=\"alert alert-danger\">The requested page could not be found.</div>";
        if ($wrapClass) {
            echo '</div>';
        }
    } else {
        echo "<div class=\"container mt-4\"><div class=\"alert alert-danger\">Page not found.</div></div>";
    }
}

if (!$skipLayout) {
    if ($module === 'public') {
        include __DIR__ . '/layout/public_footer.php';
    } else {
        include __DIR__ . '/layout/footer.php';
    }
}
