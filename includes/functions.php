<?php
// Function to get all products
function getProducts() {
    $jsonFile = __DIR__ . '/../data/products.json';
    
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        return json_decode($jsonData, true);
    }
    
    return [];
}

// Function to get a single product by ID
function getProductById($id) {
    $products = getProducts();
    
    foreach ($products as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    
    return null;
}

// Function to get products by category
function getProductsByCategory($category) {
    $products = getProducts();
    $filteredProducts = [];
    
    foreach ($products as $product) {
        if ($product['category'] == $category) {
            $filteredProducts[] = $product;
        }
    }
    
    return $filteredProducts;
}

// Function to format price
function formatPrice($price) {
    return 'LKR ' . number_format($price, 2);
}

// Function to calculate discount percentage
function calculateDiscount($originalPrice, $currentPrice) {
    if ($originalPrice <= 0) return 0;
    return round(($originalPrice - $currentPrice) / $originalPrice * 100);
}

/**
 * Generate WhatsApp order link with product details
 * 
 * @param array $product Product data
 * @param string $shopUrl The shop URL
 * @return string WhatsApp link
 */
function getWhatsAppOrderLink($product, $shopUrl = '') {
    $phone = '+94759499076'; // Your WhatsApp number
    
    if (empty($shopUrl)) {
        $shopUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }
    
    $productUrl = $shopUrl . '/product.php?id=' . $product['id'];
    
    $message = "Hello! I would like to order: *{$product['name']}* - Price: LKR {$product['price']} - Link: {$productUrl}";
    
    return "https://wa.me/{$phone}?text=" . urlencode($message);
}