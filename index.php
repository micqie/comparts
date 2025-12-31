<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config/auth.php';

// Simple routing
$module = isset($_GET['module']) ? $_GET['module'] : 'public';
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Whitelisted modules and actions
$allowedModules = [
    'public'    => ['home'],
    'auth'      => ['login', 'process_login', 'logout', 'register', 'process_register'],
    'categories' => ['list', 'save', 'delete', 'get'],
    'products'  => ['list', 'form', 'save', 'delete', 'get'],
    'customers' => ['list', 'form', 'save', 'delete', 'get'],
    'orders'    => ['list', 'form', 'save', 'view', 'delete', 'get', 'complete'],
    'reports'   => ['dashboard', 'sales'],
    'customer'  => ['dashboard', 'products', 'cart', 'checkout', 'orders', 'add_to_cart', 'remove_from_cart', 'update_cart', 'pay'],
];

// Allow auth and public modules without login check
$publicModules = ['auth', 'public'];

// Check authentication for protected modules
if (!in_array($module, $publicModules, true)) {
    requireLogin();

    // Admin-only modules (except 'get' action which can be accessed by customers for API calls)
    $adminModules = ['categories', 'products', 'customers', 'orders', 'reports'];
    if (in_array($module, $adminModules, true) && !isAdmin() && $action !== 'get') {
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

// Common layout (skip for auth pages to avoid showing nav, and API endpoints)
$skipLayout = ($module === 'auth' && in_array($action, ['login', 'register'])) || in_array($action, ['get', 'pay']);

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
$wrapClass = ($module === 'public' || $skipLayout) ? '' : 'main-content';

// Inline public home (no separate file)
if ($module === 'public' && $action === 'home') {
    ?>
    <section id="hero" class="hero-section" style="background-image: url('https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1600&q=80');">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center text-white">
            <h1 class="display-4 fw-bold">Premium Computer Parts & Fast Ordering</h1>
            <p class="lead">Build, upgrade, or scale your rigs with trusted components.</p>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#authModal" onclick="showRegisterForm()">
                    <i class="bi bi-person-plus"></i> Get Started
                </button>
                <button type="button" class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#authModal" onclick="showLoginForm()">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-4">
                <h2 class="fw-bold">Why Choose Comparts</h2>
                <p class="text-muted">Top-tier parts, transparent pricing, and secure checkout.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body">
                            <div class="icon-display mb-3 text-primary"><i class="bi bi-cpu-fill"></i></div>
                            <h5 class="card-title">Curated Components</h5>
                            <p class="card-text">CPUs, GPUs, memory, and storage from trusted brands.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body">
                            <div class="icon-display mb-3 text-primary"><i class="bi bi-truck"></i></div>
                            <h5 class="card-title">Fast Processing</h5>
                            <p class="card-text">Streamlined ordering and quick fulfillment for your builds.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body">
                            <div class="icon-display mb-3 text-primary"><i class="bi bi-shield-lock"></i></div>
                            <h5 class="card-title">Secure Checkout</h5>
                            <p class="card-text">Protected sessions and clear order history for peace of mind.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="about">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80"
                         class="img-fluid rounded shadow" alt="Workstation">
                </div>
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-3">About Comparts</h3>
                    <p class="text-muted mb-3">We help builders, gamers, and IT teams get the right components without friction. From curated catalogs to transparent stock and order tracking, Comparts keeps your builds moving.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Trusted vendor catalog</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Fast, secure checkout</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Admin and customer dashboards</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Reports and analytics for sales</li>
                    </ul>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#authModal" onclick="showRegisterForm()">
                        Start Ordering
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="contact">
        <div class="container">
            <div class="row text-center mb-4">
                <h2 class="fw-bold">Contact Us</h2>
                <p class="text-muted">We’d love to hear from you.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Reach out</h5>
                            <p class="text-muted mb-1"><i class="bi bi-geo-alt text-primary me-2"></i>123 Tech Park, Silicon Avenue</p>
                            <p class="text-muted mb-1"><i class="bi bi-telephone text-primary me-2"></i>+1 (800) 123-4567</p>
                            <p class="text-muted mb-3"><i class="bi bi-envelope text-primary me-2"></i>support@comparts.local</p>
                            <p class="text-muted">We typically respond within one business day.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Send a message</h5>
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Your name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" placeholder="name@example.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" rows="4" placeholder="How can we help?"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary w-100" disabled>
                                    <i class="bi bi-send"></i> Send (demo)
                                </button>
                                <small class="text-muted d-block mt-2">Demo only — hook up backend/email as needed.</small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
} elseif (file_exists($moduleFile)) {
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
