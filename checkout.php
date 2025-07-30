<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php?redirect=checkout.php');
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get delivery zones
$stmt = $pdo->query("SELECT * FROM delivery_zones WHERE is_active = 1");
$delivery_zones = $stmt->fetchAll();

// Calculate totals
$subtotal = 0;
$delivery_fee = 200; // Default
$tax_rate = 0.16;
$tax_amount = 0;
$total = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['total_price'];
}
$tax_amount = $subtotal * $tax_rate;
$total = $subtotal + $delivery_fee + $tax_amount;

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_address = sanitize_input($_POST['delivery_address']);
    $delivery_phone = sanitize_input($_POST['delivery_phone']);
    $delivery_instructions = sanitize_input($_POST['delivery_instructions']);
    $payment_method = sanitize_input($_POST['payment_method']);
    $delivery_zone_id = (int)$_POST['delivery_zone'];
    
    // Get delivery fee from selected zone
    $stmt = $pdo->prepare("SELECT delivery_fee FROM delivery_zones WHERE id = ?");
    $stmt->execute([$delivery_zone_id]);
    $zone = $stmt->fetch();
    $delivery_fee = $zone ? $zone['delivery_fee'] : 200;
    
    // Recalculate total with actual delivery fee
    $total = $subtotal + $delivery_fee + $tax_amount;
    
    // Generate order number
    $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
    
    try {
        $pdo->beginTransaction();
        
        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, delivery_fee, tax_amount, payment_method, delivery_address, delivery_phone, delivery_instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $order_number, $total, $delivery_fee, $tax_amount, $payment_method, $delivery_address, $delivery_phone, $delivery_instructions]);
        
        $order_id = $pdo->lastInsertId();
        
        // Add order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['product_name'], $item['quantity'], $item['unit_price'], $item['total_price']]);
        }
        
        // If M-Pesa payment, redirect to payment page
        if ($payment_method === 'mpesa') {
            $_SESSION['pending_order_id'] = $order_id;
            $_SESSION['pending_order_total'] = $total;
            redirect('mpesa_payment.php');
        }
        
        // For other payment methods, mark as paid and redirect to success
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', status = 'confirmed' WHERE id = ?");
        $stmt->execute([$order_id]);
        
        $pdo->commit();
        
        // Clear cart and redirect to success page
        unset($_SESSION['cart']);
        redirect('order_success.php?order_id=' . $order_id);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Failed to process order. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FoodExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Checkout Section -->
    <section class="py-5" style="margin-top: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-5 fw-bold mb-4">Checkout</h1>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <!-- Delivery Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4">
                                    <i class="fas fa-truck me-2"></i>Delivery Information
                                </h5>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="delivery_address" class="form-label">Delivery Address *</label>
                                        <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="delivery_phone" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control" id="delivery_phone" name="delivery_phone" 
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="delivery_zone" class="form-label">Delivery Zone *</label>
                                        <select class="form-select" id="delivery_zone" name="delivery_zone" required>
                                            <option value="">Select delivery zone</option>
                                            <?php foreach($delivery_zones as $zone): ?>
                                                <option value="<?php echo $zone['id']; ?>" data-fee="<?php echo $zone['delivery_fee']; ?>">
                                                    <?php echo htmlspecialchars($zone['name']); ?> - KSh <?php echo number_format($zone['delivery_fee'], 2); ?> 
                                                    (<?php echo htmlspecialchars($zone['estimated_time']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="delivery_instructions" class="form-label">Delivery Instructions</label>
                                        <textarea class="form-control" id="delivery_instructions" name="delivery_instructions" rows="3" 
                                                  placeholder="Any special instructions for delivery..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-4">
                                    <i class="fas fa-credit-card me-2"></i>Payment Method
                                </h5>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="mpesa" value="mpesa" checked>
                                            <label class="form-check-label" for="mpesa">
                                                <div class="mpesa-section">
                                                    <div class="mpesa-logo">
                                                        <i class="fas fa-mobile-alt fa-2x text-success"></i>
                                                    </div>
                                                    <h6>M-Pesa</h6>
                                                    <small>Pay with M-Pesa</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash">
                                            <label class="form-check-label" for="cash">
                                                <div class="text-center p-3 border rounded">
                                                    <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                    <h6>Cash on Delivery</h6>
                                                    <small>Pay when you receive</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                                            <label class="form-check-label" for="card">
                                                <div class="text-center p-3 border rounded">
                                                    <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                                    <h6>Credit/Debit Card</h6>
                                                    <small>Pay with card</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Order Summary</h5>

                                <!-- Cart Items -->
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></span>
                                        <span>KSh <?php echo number_format($item['total_price'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>

                                <hr>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>KSh <?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Delivery Fee:</span>
                                    <span id="delivery-fee">KSh <?php echo number_format($delivery_fee, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>VAT (16%):</span>
                                    <span>KSh <?php echo number_format($tax_amount, 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-bold fs-5">Total:</span>
                                    <span class="fw-bold fs-5" id="total-amount">KSh <?php echo number_format($total, 2); ?></span>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg">
                                    <i class="fas fa-lock me-2"></i>Place Order
                                </button>

                                <div class="text-center mt-3">
                                    <a href="cart.php" class="text-decoration-none">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update delivery fee and total when zone changes
        document.getElementById('delivery_zone').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const deliveryFee = parseFloat(selectedOption.dataset.fee) || 200;
            const subtotal = <?php echo $subtotal; ?>;
            const taxAmount = <?php echo $tax_amount; ?>;
            const total = subtotal + deliveryFee + taxAmount;
            
            document.getElementById('delivery-fee').textContent = 'KSh ' + deliveryFee.toFixed(2);
            document.getElementById('total-amount').textContent = 'KSh ' + total.toFixed(2);
        });
    </script>
</body>
</html> 