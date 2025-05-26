<?php
require_once 'auth.php';
require_once '../includes/functions.php';

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonFile = __DIR__ . '/../data/products.json';
    $products = getProducts();
    
    // Create data directory if it doesn't exist
    if (!file_exists(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
    }
    
    if ($action === 'add') {
        // Generate new product ID
        $newId = 1;
        if (!empty($products)) {
            $ids = array_column($products, 'id');
            $newId = max($ids) + 1;
        }
        
        // Handle image uploads
        $images = [];
        $thumbnail = '';
        
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = __DIR__ . '/../assets/images/products/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = time() . '_' . $_FILES['images']['name'][$key];
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $images[] = $fileName;
                    
                    // Use first image as thumbnail
                    if (empty($thumbnail)) {
                        $thumbnail = $fileName;
                    }
                }
            }
        }
        
        // Create new product
        $newProduct = [
            'id' => $newId,
            'name' => $_POST['name'],
            'category' => $_POST['category'],
            'price' => floatval($_POST['price']),
            'original_price' => floatval($_POST['original_price']),
            'discount' => calculateDiscount($_POST['original_price'], $_POST['price']),
            'description' => $_POST['description'],
            'images' => $images,
            'thumbnail' => $thumbnail,
            'stock' => intval($_POST['stock']),
            'rating' => 0,
            'reviews' => 0,
            'sold' => 0
        ];
        
        // Add sizes if provided
        if (!empty($_POST['sizes'])) {
            $newProduct['sizes'] = explode(',', $_POST['sizes']);
        }
        
        // Add colors if provided
        if (!empty($_POST['colors'])) {
            $newProduct['colors'] = explode(',', $_POST['colors']);
        }
        
        $products[] = $newProduct;
        file_put_contents($jsonFile, json_encode($products, JSON_PRETTY_PRINT));
        
        header('Location: products.php?success=added');
        exit;
    } elseif ($action === 'edit' && $productId > 0) {
        // Find product index
        $productIndex = -1;
        foreach ($products as $index => $product) {
            if ($product['id'] === $productId) {
                $productIndex = $index;
                break;
            }
        }
        
        if ($productIndex >= 0) {
            // Handle image uploads
            $images = $products[$productIndex]['images'];
            $thumbnail = $products[$productIndex]['thumbnail'];
            
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = __DIR__ . '/../assets/images/products/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Clear existing images if requested
                if (isset($_POST['clear_images'])) {
                    $images = [];
                    $thumbnail = '';
                }
                
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === 0) {
                        $fileName = time() . '_' . $_FILES['images']['name'][$key];
                        $filePath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($tmp_name, $filePath)) {
                            $images[] = $fileName;
                            
                            // Use first image as thumbnail if no thumbnail exists
                            if (empty($thumbnail)) {
                                $thumbnail = $fileName;
                            }
                        }
                    }
                }
            }
            
            // Update product
            $products[$productIndex]['name'] = $_POST['name'];
            $products[$productIndex]['category'] = $_POST['category'];
            $products[$productIndex]['price'] = floatval($_POST['price']);
            $products[$productIndex]['original_price'] = floatval($_POST['original_price']);
            $products[$productIndex]['discount'] = calculateDiscount($_POST['original_price'], $_POST['price']);
            $products[$productIndex]['description'] = $_POST['description'];
            $products[$productIndex]['images'] = $images;
            $products[$productIndex]['thumbnail'] = $thumbnail;
            $products[$productIndex]['stock'] = intval($_POST['stock']);
            
            // Update sizes if provided
            if (isset($_POST['sizes'])) {
                $products[$productIndex]['sizes'] = explode(',', $_POST['sizes']);
            } else {
                unset($products[$productIndex]['sizes']);
            }
            
            // Update colors if provided
            if (isset($_POST['colors'])) {
                $products[$productIndex]['colors'] = explode(',', $_POST['colors']);
            } else {
                unset($products[$productIndex]['colors']);
            }
            
            file_put_contents($jsonFile, json_encode($products, JSON_PRETTY_PRINT));
            
            header('Location: products.php?success=updated');
            exit;
        }
    } elseif ($action === 'delete' && $productId > 0) {
        // Find product index
        $productIndex = -1;
        foreach ($products as $index => $product) {
            if ($product['id'] === $productId) {
                $productIndex = $index;
                break;
            }
        }
        
        if ($productIndex >= 0) {
            // Remove product
            array_splice($products, $productIndex, 1);
            file_put_contents($jsonFile, json_encode($products, JSON_PRETTY_PRINT));
            
            header('Location: products.php?success=deleted');
            exit;
        }
    }
}

// Get product for editing
$editProduct = null;
if ($action === 'edit' && $productId > 0) {
    $editProduct = getProductById($productId);
    
    if (!$editProduct) {
        header('Location: products.php');
        exit;
    }
}

// Get all products for listing
$products = getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - MiniMart Lanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #343a40;
        }
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 15px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar i {
            margin-right: 10px;
        }
        .product-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">MiniMart Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">View Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0 sidebar">
                <div class="pt-3">
                    <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
                    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <?php if (isset($_GET['success'])): ?>
                    <?php if ($_GET['success'] === 'added'): ?>
                        <div class="alert alert-success">Product added successfully!</div>
                    <?php elseif ($_GET['success'] === 'updated'): ?>
                        <div class="alert alert-success">Product updated successfully!</div>
                    <?php elseif ($_GET['success'] === 'deleted'): ?>
                        <div class="alert alert-success">Product deleted successfully!</div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($action === 'list'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Products</h2>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Product
                        </a>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($products)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No products found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td><?= $product['id'] ?></td>
                                                    <td>
                                                        <?php if (!empty($product['thumbnail'])): ?>
                                                            <img src="../assets/images/products/<?= $product['thumbnail'] ?>" class="product-thumbnail" alt="<?= $product['name'] ?>">
                                                        <?php else: ?>
                                                            <span class="text-muted">No image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $product['name'] ?></td>
                                                    <td><?= $product['category'] ?></td>
                                                    <td><?= formatPrice($product['price']) ?></td>
                                                    <td><?= $product['stock'] ?></td>
                                                    <td>
                                                        <a href="?action=edit&id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $product['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        
                                                        <!-- Delete Confirmation Modal -->
                                                        <div class="modal fade" id="deleteModal<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete the product "<?= $product['name'] ?>"?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <form method="post" action="?action=delete&id=<?= $product['id'] ?>">
                                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php elseif ($action === 'add' || $action === 'edit'): ?>
                    <h2><?= $action === 'add' ? 'Add New Product' : 'Edit Product' ?></h2>
                    <div class="card">
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?= $editProduct['name'] ?? '' ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="clothing" <?= (isset($editProduct['category']) && $editProduct['category'] === 'clothing') ? 'selected' : '' ?>>Clothing</option>
                                            <option value="electronics" <?= (isset($editProduct['category']) && $editProduct['category'] === 'electronics') ? 'selected' : '' ?>>Electronics</option>
                                            <option value="accessories" <?= (isset($editProduct['category']) && $editProduct['category'] === 'accessories') ? 'selected' : '' ?>>Accessories</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price (LKR)</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $editProduct['price'] ?? '' ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="original_price" class="form-label">Original Price (LKR)</label>
                                        <input type="number" step="0.01" class="form-control" id="original_price" name="original_price" value="<?= $editProduct['original_price'] ?? '' ?>">
                                        <small class="text-muted">Leave empty if there's no discount</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?= $editProduct['description'] ?? '' ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" class="form-control" id="stock" name="stock" value="<?= $editProduct['stock'] ?? '10' ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="sizes" class="form-label">Sizes (comma separated)</label>
                                        <input type="text" class="form-control" id="sizes" name="sizes" value="<?= isset($editProduct['sizes']) ? implode(',', $editProduct['sizes']) : '' ?>" placeholder="S,M,L,XL">
                                        <small class="text-muted">Leave empty if not applicable</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="colors" class="form-label">Colors (comma separated)</label>
                                    <input type="text" class="form-control" id="colors" name="colors" value="<?= isset($editProduct['colors']) ? implode(',', $editProduct['colors']) : '' ?>" placeholder="Red,Blue,Black">
                                    <small class="text-muted">Leave empty if not applicable</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="images" class="form-label">Product Images</label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                    <small class="text-muted">You can select multiple images. The first image will be used as the thumbnail.</small>
                                </div>
                                
                                <?php if ($action === 'edit' && !empty($editProduct['images'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Current Images</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($editProduct['images'] as $image): ?>
                                                <div class="position-relative">
                                                    <img src="../assets/images/products/<?= $image ?>" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="clear_images" name="clear_images">
                                            <label class="form-check-label" for="clear_images">
                                                Clear all images and replace with new ones
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <?= $action === 'add' ? 'Add Product' : 'Update Product' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>