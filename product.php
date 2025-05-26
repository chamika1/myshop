<?php
require_once 'includes/functions.php';

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product details
$product = getProductById($productId);

// Redirect if product not found
if (!$product) {
    header('Location: index.php');
    exit;
}

include 'includes/header.php';
?>

<div class="row">
    <!-- Product Images -->
    <div class="col-md-6 mb-4">
        <img src="assets/images/products/<?= $product['images'][0] ?>" class="product-detail-image" alt="<?= $product['name'] ?>">
        
        <div class="thumbnail-container mt-3">
            <?php foreach ($product['images'] as $index => $image): ?>
            <img src="assets/images/products/<?= $image ?>" 
                 class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                 alt="<?= $product['name'] ?> thumbnail">
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Product Details -->
    <div class="col-md-6">
        <h1 class="mb-3"><?= $product['name'] ?></h1>
        
        <div class="mb-3">
            <?php if (isset($product['rating'])): ?>
            <div class="rating mb-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= floor($product['rating'])): ?>
                        <i class="fas fa-star"></i>
                    <?php elseif ($i - 0.5 <= $product['rating']): ?>
                        <i class="fas fa-star-half-alt"></i>
                    <?php else: ?>
                        <i class="far fa-star"></i>
                    <?php endif; ?>
                <?php endfor; ?>
                <span class="ms-2"><?= $product['rating'] ?></span>
                <span class="text-muted">(<?= $product['reviews'] ?? 0 ?> reviews)</span>
            </div>
            <?php endif; ?>
            
            <?php if (isset($product['sold'])): ?>
            <div class="text-muted mb-2"><?= $product['sold'] ?>+ sold</div>
            <?php endif; ?>
        </div>
        
        <div class="mb-4">
            <?php if (isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
            <div class="original-price"><?= formatPrice($product['original_price']) ?></div>
            <?php endif; ?>
            <div class="current-price fs-3"><?= formatPrice($product['price']) ?></div>
            <?php if (isset($product['discount']) && $product['discount'] > 0): ?>
            <span class="badge bg-danger"><?= $product['discount'] ?>% OFF</span>
            <?php endif; ?>
        </div>
        
        <?php if (isset($product['description'])): ?>
        <div class="mb-4">
            <h5>Description</h5>
            <p><?= $product['description'] ?></p>
        </div>
        <?php endif; ?>
        
        <?php if (isset($product['sizes']) && count($product['sizes']) > 0): ?>
        <div class="mb-4">
            <h5>Size</h5>
            <div class="btn-group" role="group">
                <?php foreach ($product['sizes'] as $size): ?>
                <input type="radio" class="btn-check" name="size" id="size-<?= $size ?>" autocomplete="off">
                <label class="btn btn-outline-secondary" for="size-<?= $size ?>"><?= $size ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($product['colors']) && count($product['colors']) > 0): ?>
        <div class="mb-4">
            <h5>Color</h5>
            <div class="btn-group" role="group">
                <?php foreach ($product['colors'] as $color): ?>
                <input type="radio" class="btn-check" name="color" id="color-<?= $color ?>" autocomplete="off">
                <label class="btn btn-outline-secondary" for="color-<?= $color ?>"><?= $color ?></label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mb-4">
            <h5>Quantity</h5>
            <div class="input-group" style="width: 150px;">
                <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">-</button>
                <input type="text" class="form-control text-center" id="quantity" value="1">
                <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
            </div>
        </div>
        
        <div class="d-grid gap-2">
            <div class="mb-4">
                <button class="btn btn-primary btn-lg w-100 mb-3" id="add-to-cart-btn">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                
                <!-- WhatsApp Order Button -->
                <button class="btn btn-success btn-lg w-100" id="whatsapp-order-btn">
                    <i class="fab fa-whatsapp"></i> Order via WhatsApp
                </button>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add to cart functionality
                const addToCartBtn = document.getElementById('add-to-cart-btn');
                const whatsappOrderBtn = document.getElementById('whatsapp-order-btn');
                const quantityInput = document.getElementById('quantity');
                const increaseBtn = document.getElementById('increase-quantity');
                const decreaseBtn = document.getElementById('decrease-quantity');
                
                // Quantity controls
                increaseBtn.addEventListener('click', function() {
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                });
                
                decreaseBtn.addEventListener('click', function() {
                    if (parseInt(quantityInput.value) > 1) {
                        quantityInput.value = parseInt(quantityInput.value) - 1;
                    }
                });
                
                // Add to cart
                addToCartBtn.addEventListener('click', function() {
                    const productId = '<?= $product['id'] ?>';
                    const productName = '<?= $product['name'] ?>';
                    const productPrice = <?= $product['price'] ?>;
                    const productImage = '<?= $product['thumbnail'] ?>';
                    const quantity = parseInt(quantityInput.value);
                    
                    // Get cart from localStorage
                    let cart = JSON.parse(localStorage.getItem('cart')) || [];
                    
                    // Check if product is already in cart
                    const existingProduct = cart.find(item => item.id === productId);
                    
                    if (existingProduct) {
                        existingProduct.quantity += quantity;
                    } else {
                        cart.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            image: productImage,
                            quantity: quantity
                        });
                    }
                    
                    // Save cart to localStorage
                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    // Update cart count
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    
                    cartCountElements.forEach(element => {
                        element.textContent = totalItems;
                    });
                    
                    // Show notification
                    alert(`${productName} added to cart!`);
                });
                
                // WhatsApp order functionality
                whatsappOrderBtn.addEventListener('click', function() {
                    const phone = '+94759499076'; // Your WhatsApp number
                    const productName = '<?= $product['name'] ?>';
                    const productPrice = <?= $product['price'] ?>;
                    const productUrl = window.location.href;
                    const quantity = parseInt(quantityInput.value);
                    
                    const message = `Hello! I would like to order: *${productName}* - Quantity: ${quantity} - Price: LKR ${productPrice.toFixed(2)} - Link: ${productUrl}`;
                    const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                    
                    window.open(whatsappUrl, '_blank');
                });
            });
            </script>
            
            <a href="cart.php" class="btn btn-success">Buy Now</a>
        </div>
        
        <div class="mt-4">
            <h5>Delivery</h5>
            <p><i class="fas fa-truck me-2"></i> Cash on Delivery available</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>