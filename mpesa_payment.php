<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and has pending order
if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order_id'])) {
    redirect('index.php');
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$_SESSION['pending_order_id'], $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

// Handle M-Pesa payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_number = sanitize_input($_POST['phone_number']);
    $amount = $_SESSION['pending_order_total'];
    
    // Validate phone number (Kenyan format)
    if (!preg_match('/^(\+254|254|0)?([17]\d{8})$/', $phone_number)) {
        $error = 'Please enter a valid Kenyan phone number';
    } else {
        // Format phone number to 254 format
        $phone_number = preg_replace('/^(\+254|254|0)?/', '254', $phone_number);
        
        try {
            // Generate transaction ID
            $transaction_id = 'TXN' . date('YmdHis') . rand(1000, 9999);
            
            // Store transaction in database
            $stmt = $pdo->prepare("INSERT INTO mpesa_transactions (order_id, transaction_id, phone_number, amount) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order['id'], $transaction_id, $phone_number, $amount]);
            
            // In a real implementation, you would call M-Pesa API here
            // For demo purposes, we'll simulate the payment process
            
            // Simulate M-Pesa STK push
            $success = 'M-Pesa payment initiated successfully! Please check your phone for the payment prompt.';
            
            // Update order status
            $stmt = $pdo->prepare("UPDATE orders SET mpesa_transaction_id = ?, status = 'confirmed' WHERE id = ?");
            $stmt->execute([$transaction_id, $order['id']]);
            
            // Clear session and redirect to success
            unset($_SESSION['pending_order_id']);
            unset($_SESSION['pending_order_total']);
            unset($_SESSION['cart']);
            
            redirect('order_success.php?order_id=' . $order['id']);
            
        } catch (Exception $e) {
            $error = 'Failed to initiate payment. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment - FoodExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- M-Pesa Payment Section -->
    <section class="py-5" style="margin-top: 80px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="mpesa-logo mx-auto">
                                    <i class="fas fa-mobile-alt fa-2x text-success"></i>
                                </div>
                                <h2 class="fw-bold mt-3">M-Pesa Payment</h2>
                                <p class="text-muted">Complete your payment using M-Pesa</p>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Order Summary -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Order Summary</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Order Number:</span>
                                        <span class="fw-bold"><?php echo htmlspecialchars($order['order_number']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Amount:</span>
                                        <span class="fw-bold text-success">KSh <?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label for="phone_number" class="form-label">M-Pesa Phone Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                               value="<?php echo htmlspecialchars($user['phone']); ?>" 
                                               placeholder="e.g., 254700000000" required>
                                    </div>
                                    <small class="text-muted">Enter the phone number registered with M-Pesa</small>
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Payment Instructions:</h6>
                                    <ol class="mb-0">
                                        <li>Enter your M-Pesa registered phone number</li>
                                        <li>Click "Pay with M-Pesa" button</li>
                                        <li>You will receive an M-Pesa prompt on your phone</li>
                                        <li>Enter your M-Pesa PIN to complete payment</li>
                                        <li>You will receive a confirmation SMS</li>
                                    </ol>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-mobile-alt me-2"></i>Pay with M-Pesa
                                    </button>
                                    <a href="checkout.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Checkout
                                    </a>
                                </div>
                            </form>

                            <!-- M-Pesa Features -->
                            <div class="mt-4">
                                <h6 class="text-center mb-3">Why M-Pesa?</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                        <p class="small">Secure</p>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-bolt fa-2x text-warning mb-2"></i>
                                        <p class="small">Instant</p>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                        <p class="small">Convenient</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format phone number input
        document.getElementById('phone_number').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.startsWith('254')) {
                this.value = value;
            } else if (value.startsWith('0')) {
                this.value = '254' + value.substring(1);
            } else if (value.length > 0) {
                this.value = '254' + value;
            }
        });
    </script>
</body>
</html> 