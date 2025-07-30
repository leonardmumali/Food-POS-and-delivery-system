<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['order_id'])) {
    redirect('index.php');
}

$order_id = (int)$_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.phone as customer_phone 
                       FROM orders o 
                       LEFT JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}

// Get order items
$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - FoodExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Success Section -->
    <section class="py-5" style="margin-top: 80px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-check fa-2x"></i>
                                </div>
                            </div>
                            
                            <h1 class="display-6 fw-bold text-success mb-3">Order Confirmed!</h1>
                            <p class="lead text-muted mb-4">Thank you for your order. We're preparing your delicious food!</p>
                            
                            <div class="alert alert-success">
                                <strong>Order Number:</strong> <?php echo htmlspecialchars($order['order_number']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-receipt me-2"></i>Order Details
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Customer Information</h6>
                                    <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                    <p class="mb-1"><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                                    <?php if ($order['delivery_instructions']): ?>
                                        <p class="mb-1"><strong>Instructions:</strong> <?php echo htmlspecialchars($order['delivery_instructions']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6>Order Information</h6>
                                    <p class="mb-1"><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                                    <p class="mb-1"><strong>Payment Method:</strong> 
                                        <?php 
                                        switch($order['payment_method']) {
                                            case 'mpesa': echo 'M-Pesa'; break;
                                            case 'cash': echo 'Cash on Delivery'; break;
                                            case 'card': echo 'Credit/Debit Card'; break;
                                            default: echo ucfirst($order['payment_method']);
                                        }
                                        ?>
                                    </p>
                                    <p class="mb-1"><strong>Payment Status:</strong> 
                                        <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                    </p>
                                    <p class="mb-1"><strong>Order Status:</strong> 
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $order['status'])); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-utensils me-2"></i>Order Items
                            </h5>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                <td class="text-end">KSh <?php echo number_format($item['unit_price'], 2); ?></td>
                                                <td class="text-end">KSh <?php echo number_format($item['total_price'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                            <td class="text-end">KSh <?php echo number_format($order['total_amount'] - $order['delivery_fee'] - $order['tax_amount'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Delivery Fee:</strong></td>
                                            <td class="text-end">KSh <?php echo number_format($order['delivery_fee'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>VAT (16%):</strong></td>
                                            <td class="text-end">KSh <?php echo number_format($order['tax_amount'], 2); ?></td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong>KSh <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Tracking -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-truck me-2"></i>Order Tracking
                            </h5>

                            <div class="row">
                                <div class="col-12">
                                    <div class="progress mb-3" style="height: 8px;">
                                        <?php
                                        $progress = 0;
                                        switch($order['status']) {
                                            case 'pending': $progress = 20; break;
                                            case 'confirmed': $progress = 40; break;
                                            case 'preparing': $progress = 60; break;
                                            case 'out_for_delivery': $progress = 80; break;
                                            case 'delivered': $progress = 100; break;
                                            case 'cancelled': $progress = 0; break;
                                        }
                                        ?>
                                        <div class="progress-bar bg-success" style="width: <?php echo $progress; ?>%"></div>
                                    </div>

                                    <div class="row text-center">
                                        <div class="col">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <small>Order Placed</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="bg-<?php echo in_array($order['status'], ['confirmed', 'preparing', 'out_for_delivery', 'delivered']) ? 'success' : 'secondary'; ?> text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <small>Confirmed</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="bg-<?php echo in_array($order['status'], ['preparing', 'out_for_delivery', 'delivered']) ? 'success' : 'secondary'; ?> text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-utensils"></i>
                                                </div>
                                                <small>Preparing</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="bg-<?php echo in_array($order['status'], ['out_for_delivery', 'delivered']) ? 'success' : 'secondary'; ?> text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-truck"></i>
                                                </div>
                                                <small>On the Way</small>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="bg-<?php echo $order['status'] === 'delivered' ? 'success' : 'secondary'; ?> text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-home"></i>
                                                </div>
                                                <small>Delivered</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="orders.php" class="btn btn-primary me-3">
                            <i class="fas fa-list me-2"></i>View All Orders
                        </a>
                        <a href="menu.php" class="btn btn-outline-primary">
                            <i class="fas fa-utensils me-2"></i>Order More Food
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 