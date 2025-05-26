<?php
require_once 'auth.php';
require_once '../includes/functions.php';

// Get orders (placeholder for now)
$orders = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - MiniMart Lanka</title>
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
        .order-status {
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
        }
        .status-processing {
            color: #17a2b8;
        }
        .status-shipped {
            color: #007bff;
        }
        .status-delivered {
            color: #28a745;
        }
        .status-cancelled {
            color: #dc3545;
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
                    <a href="products.php"><i class="fas fa-box"></i> Products</a>
                    <a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
                </div>
            </div>
            <div class="col-md-10 p-4">
                <h2>Orders</h2>
                <hr>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                <h5>No orders yet</h5>
                                <p>Orders will appear here when customers make purchases.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?= $order['id'] ?></td>
                                                <td><?= $order['customer_name'] ?></td>
                                                <td><?= date('M d, Y', strtotime($order['date'])) ?></td>
                                                <td><?= formatPrice($order['total']) ?></td>
                                                <td>
                                                    <span class="order-status status-<?= strtolower($order['status']) ?>">
                                                        <?= $order['status'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id'] ?>">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Order Modals -->
                            <?php foreach ($orders as $order): ?>
                                <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order #<?= $order['id'] ?> Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <h6>Customer Information</h6>
                                                        <p>
                                                            <strong>Name:</strong> <?= $order['customer_name'] ?><br>
                                                            <strong>Email:</strong> <?= $order['customer_email'] ?><br>
                                                            <strong>Phone:</strong> <?= $order['customer_phone'] ?><br>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Shipping Address</h6>
                                                        <p><?= $order['shipping_address'] ?></p>
                                                    </div>
                                                </div>
                                                
                                                <h6>Order Items</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Price</th>
                                                                <th>Quantity</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($order['items'] as $item): ?>
                                                                <tr>
                                                                    <td><?= $item['name'] ?></td>
                                                                    <td><?= formatPrice($item['price']) ?></td>
                                                                    <td><?= $item['quantity'] ?></td>
                                                                    <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                                <td><?= formatPrice($order['subtotal']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                                                <td><?= formatPrice($order['shipping']) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                                <td><strong><?= formatPrice($order['total']) ?></strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <h6>Order Status</h6>
                                                    <form method="post" action="update_order_status.php">
                                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                        <div class="input-group">
                                                            <select class="form-select" name="status">
                                                                <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                                <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                                                <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                                                <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                            </select>
                                                            <button class="btn btn-primary" type="submit">Update Status</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>