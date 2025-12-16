<?php
// Public homepage with hero and featured content
?>
<section class="hero-section" style="background-image: url('https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1600&q=80');">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-white">
        <h1 class="display-4 fw-bold">Premium Computer Parts & Fast Ordering</h1>
        <p class="lead">Build, upgrade, or scale your rigs with trusted components.</p>
        <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="index.php?module=auth&action=register" class="btn btn-primary btn-lg">
                <i class="bi bi-person-plus"></i> Get Started
            </a>
            <a href="index.php?module=auth&action=login" class="btn btn-outline-light btn-lg">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
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

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80"
                     class="img-fluid rounded shadow" alt="Workstation">
            </div>
            <div class="col-lg-6">
                <h3 class="fw-bold mb-3">Built for builders, gamers, and creators</h3>
                <p class="text-muted mb-3">From high-performance GPUs to rock-solid storage, Comparts simplifies sourcing the parts you need.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Real-time stock visibility</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Track orders and history</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Dedicated admin dashboard</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-primary me-2"></i>Customer cart & checkout</li>
                </ul>
                <a href="index.php?module=auth&action=register" class="btn btn-primary">
                    Start Ordering
                </a>
            </div>
        </div>
    </div>
</section>
