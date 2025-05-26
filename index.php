<?php
require_once 'includes/functions.php';

// Get category filter if set
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Get products
$products = $category ? getProductsByCategory($category) : getProducts();

include 'includes/header.php';
?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2><i class="fas fa-gift me-2"></i>Welcome to MiniMart Lanka</h2>
            <p class="mb-md-0">Discover quality products at amazing prices with free shipping!</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="#products" class="btn btn-light">
                <i class="fas fa-shopping-bag me-2"></i>Shop Now
            </a>
        </div>
    </div>
</div>

<!-- Products Section -->
<section id="products">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= $category ? ucfirst($category) : 'All Products' ?></h2>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-sort me-1"></i> Sort By
            </button>
            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                <li><a class="dropdown-item" href="#">Price: Low to High</a></li>
                <li><a class="dropdown-item" href="#">Price: High to Low</a></li>
                <li><a class="dropdown-item" href="#">Popularity</a></li>
                <li><a class="dropdown-item" href="#">Newest First</a></li>
            </ul>
        </div>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($products as $product): ?>
        <div class="col">
            <div class="card product-card h-100">
                <?php if (isset($product['discount']) && $product['discount'] > 0): ?>
                <div class="discount-badge">-<?= $product['discount'] ?>%</div>
                <?php endif; ?>
                
                <a href="product.php?id=<?= $product['id'] ?>">
                    <img src="https://via.placeholder.com/300x200?text=<?= urlencode($product['name']) ?>" class="card-img-top product-image" alt="<?= $product['name'] ?>">
                </a>
                
                <div class="card-body">
                    <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                        <h5 class="product-title"><?= $product['name'] ?></h5>
                    </a>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                            <span class="text-muted text-decoration-line-through me-2">
                                <?= formatPrice($product['original_price']) ?>
                            </span>
                            <?php endif; ?>
                            <span class="fw-bold current-price"><?= formatPrice($product['price']) ?></span>
                        </div>
                        
                        <?php if (isset($product['rating'])): ?>
                        <div class="text-warning">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $product['rating']): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif ($i - 0.5 <= $product['rating']): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm add-to-cart"
                                data-id="<?= $product['id'] ?>"
                                data-name="<?= $product['name'] ?>"
                                data-price="<?= $product['price'] ?>"
                                data-image="<?= $product['thumbnail'] ?>">
                            <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                        </button>
                        <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>