<?php
require_once 'includes/functions.php';
include 'includes/header.php';
?>

<h1 class="mb-4">Shopping Cart</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <h4 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h4>
                <div id="cart-items">
                    <!-- Cart items will be loaded here via JavaScript -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Order Summary</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="subtotal">LKR 0.00</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping:</span>
                    <span class="text-success">Free</span>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong id="total" class="text-primary">LKR 0.00</strong>
                </div>
                
                <!-- WhatsApp Checkout Button -->
                <button class="btn btn-success w-100 py-2 mb-3" id="whatsapp-checkout-btn" disabled>
                    <i class="fab fa-whatsapp me-2"></i> Order via WhatsApp
                </button>
                
                <a href="index.php" class="btn btn-outline-primary w-100">
                    <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                </a>
                
                <!-- Admin link (you might want to hide this in production) -->
                <div class="text-center mt-3">
                    <a href="admin/index.php" class="text-muted small">Admin Panel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load cart from localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsContainer = document.getElementById('cart-items');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const whatsappCheckoutBtn = document.getElementById('whatsapp-checkout-btn');
    
    // Render cart items
    renderCart();
    
    function renderCart() {
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                    <h5>Your cart is empty</h5>
                    <p>Looks like you haven't added any products to your cart yet.</p>
                    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            `;
            subtotalElement.textContent = 'LKR 0.00';
            totalElement.textContent = 'LKR 0.00';
            whatsappCheckoutBtn.disabled = true;
        } else {
            let cartHTML = '';
            let subtotal = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                cartHTML += `
                    <div class="cart-item mb-3 pb-3 border-bottom">
                        <div class="row align-items-center">
                            <div class="col-3 col-md-2">
                                <img src="${item.image}" class="img-fluid rounded" alt="${item.name}">
                            </div>
                            <div class="col-9 col-md-4">
                                <h6 class="mb-0">${item.name}</h6>
                            </div>
                            <div class="col-6 col-md-2 mt-2 mt-md-0">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary decrease-quantity" data-index="${index}">-</button>
                                    <input type="text" class="form-control text-center item-quantity" value="${item.quantity}" readonly>
                                    <button class="btn btn-outline-secondary increase-quantity" data-index="${index}">+</button>
                                </div>
                            </div>
                            <div class="col-4 col-md-2 mt-2 mt-md-0 text-end">
                                <span class="fw-bold">LKR ${item.price.toFixed(2)}</span>
                            </div>
                            <div class="col-2 col-md-2 mt-2 mt-md-0 text-end">
                                <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            cartItemsContainer.innerHTML = cartHTML;
            
            // Update totals
            subtotalElement.textContent = `LKR ${subtotal.toFixed(2)}`;
            totalElement.textContent = `LKR ${subtotal.toFixed(2)}`;
            whatsappCheckoutBtn.disabled = false;
            
            // Add WhatsApp checkout functionality
            whatsappCheckoutBtn.onclick = function() {
                const phone = '+94759499076'; // Your WhatsApp number
                let message = "Hello! I would like to order the following items:\n\n";
                
                cart.forEach(item => {
                    message += `*${item.name}* - Quantity: ${item.quantity} - Price: LKR ${item.price.toFixed(2)}\n`;
                });
                
                message += `\nTotal: LKR ${subtotal.toFixed(2)}`;
                
                const whatsappUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                window.open(whatsappUrl, '_blank');
            };
            
            // Add event listeners for quantity buttons and remove buttons
            document.querySelectorAll('.increase-quantity').forEach(button => {
                button.onclick = function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    cart[index].quantity += 1;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCart();
                    updateCartCount();
                };
            });
            
            document.querySelectorAll('.decrease-quantity').forEach(button => {
                button.onclick = function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    if (cart[index].quantity > 1) {
                        cart[index].quantity -= 1;
                        localStorage.setItem('cart', JSON.stringify(cart));
                        renderCart();
                        updateCartCount();
                    }
                };
            });
            
            document.querySelectorAll('.remove-item').forEach(button => {
                button.onclick = function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    cart.splice(index, 1);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCart();
                    updateCartCount();
                };
            });
        }
    }
    
    // Function to update cart count in header
    function updateCartCount() {
        const cartCountElements = document.querySelectorAll('.cart-count');
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        
        cartCountElements.forEach(element => {
            element.textContent = totalItems;
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>