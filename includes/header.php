<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiniMart Lanka - Quality Products at Great Prices</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-store me-2"></i>MiniMart Lanka
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <form class="d-flex mx-auto">
                        <div class="input-group">
                            <input class="form-control" type="search" placeholder="Search products..." style="border-radius: 20px 0 0 20px;">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart"></i> Cart
                                <span class="badge bg-primary rounded-pill cart-count">0</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="bg-light py-3">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <ul class="nav category-nav">
                            <li class="nav-item">
                                <a class="nav-link <?= !isset($_GET['category']) ? 'active' : '' ?>" href="index.php">All Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($_GET['category']) && $_GET['category'] == 'clothing' ? 'active' : '' ?>" href="index.php?category=clothing">Clothing</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($_GET['category']) && $_GET['category'] == 'electronics' ? 'active' : '' ?>" href="index.php?category=electronics">Electronics</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($_GET['category']) && $_GET['category'] == 'accessories' ? 'active' : '' ?>" href="index.php?category=accessories">Accessories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= isset($_GET['category']) && $_GET['category'] == 'home appliances' ? 'active' : '' ?>" href="index.php?category=home+appliances">Home Appliances</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="container py-4">