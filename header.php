<?php
session_start();
require 'includes/db.php';
?>
<!-- header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royal Man - Premium Men's Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top py-3 bg-white shadow-sm">
    <div class="container position-relative">
        <!-- Logo -->
        <a class="navbar-brand fs-2 fw-bold text-gold" href="/Royal-Man">Royal Man</a>

        <!-- Mobile Menu Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Desktop Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-4">
                <li class="nav-item"><a class="nav-link fw-500" href="/Royal-Man">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-500" href="#shop">Shop</a></li>
                <li class="nav-item"><a class="nav-link fw-500" href="#new">New</a></li>
                <li class="nav-item"><a class="nav-link fw-500" href="login">Login</a></li>

                <!-- Search Icon Button (Only Icon) -->
                <li class="nav-item">
                    <button id="searchBtn" class="btn btn-link text-dark p-2">
                        <i class="bi bi-search fs-4"></i>
                    </button>
                </li>

                <!-- Cart -->
                <li class="nav-item">
                    <a href="cart" class="btn btn-outline-dark position-relative">
                        <i class="bi bi-bag"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Full Search Overlay (Hidden by default) -->
        <div id="searchOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex align-items-center justify-content-center" style="z-index: 9999; display: none;">
            <div class="w-100 px-4" style="max-width: 1000px;">
                <div class="input_text d-flex align-items-center px-2">
                    <i class="bi bi-search text-gold fs-3 me-3"></i>
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="form-control form-control-lg border-0 shadow-none fs-3" 
                        placeholder="Search T-shirts, Hoodies, Jackets..." 
                        autofocus>
                    <button id="closeSearch" class="btn btn-link text-dark ms-3">
                        <i class="bi bi-x-lg fs-3"></i>
                    </button>
                </div>
                <div class="mt-4 text-center text-muted" id="searchHint">
                    Start typing to search products...
                </div>
            </div>
        </div>
    </div>
</nav>