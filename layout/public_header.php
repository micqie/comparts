<?php
// Public site header with top navigation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Computer Parts Ordering System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="public-body">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top public-nav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?module=public&action=home">
            <i class="bi bi-cpu"></i> Comparts
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php#hero">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#about">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#contact">Contact Us</a>
                </li>
            </ul>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#authModal" onclick="showLoginForm()">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#authModal" onclick="showRegisterForm()">
                    <i class="bi bi-person-plus"></i> Register
                </button>
            </div>
        </div>
    </div>
</nav>

<main class="public-main">
