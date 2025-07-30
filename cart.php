<?php
session_start();
require_once 'config/database.php';

// Handle cart actions
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    switch ($action) {
        case 'update_quantity':
            $quantity = (int)$_POST['quantity'];
            if ($quantity > 0) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['product_id'] == $product_id) {
                        $item['quantity'] = $quantity;
                        $item['total_price'] = $item['quantity'] * $item['unit_price'];
                        break;
                    }
                }
            }
            break;
            
        case 'remove_item':
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $product_id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            break;
            
        case 'clear_cart':
            unset($_SESSION['cart']);
            break;
    }
    
    // Redirect to prevent form resubmission
    header('Location: cart.php');
    exit;
}

// Calculate totals
$subtotal = 0;
$delivery_fee = 200; // Default delivery fee
$tax_rate = 0.16; // 16% VAT
$tax_amount = 0;
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['total_price'];
    }
    $tax_amount = $subtotal * $tax_rate;
    $total = $subtotal + $delivery_fee + $tax_amount;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - FoodExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Cart Section -->
    <section class="py-5" style="margin-top: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-5 fw-bold mb-4">Shopping Cart</h1>
                </div>
            </div>

            <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
                <div class="row">
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h3>Your cart is empty</h3>
                        <p class="text-muted">Add some delicious items to your cart to get started!</p>
                        <a href="menu.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-utensils me-2"></i>Browse Menu
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Cart Items</h5>
                                
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <div class="cart-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                <p class="text-muted mb-0">KSh <?php echo number_format($item['unit_price'], 2); ?> each</p>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="quantity-control">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="update_quantity">
                                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                        <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                                        <span class="mx-2 fw-bold"><?php echo $item['quantity']; ?></span>
                                                        <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <span class="fw-bold">KSh <?php echo number_format($item['total_price'], 2); ?></span>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="remove_item">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <div class="text-end mt-3">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="clear_cart">
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Clear entire cart?')">
                                            <i class="fas fa-trash me-2"></i>Clear Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Order Summary</h5>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>KSh <?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Delivery Fee:</span>
                                    <span>KSh <?php echo number_format($delivery_fee, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>VAT (16%):</span>
                                    <span>KSh <?php echo number_format($tax_amount, 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-bold fs-5">Total:</span>
                                    <span class="fw-bold fs-5">KSh <?php echo number_format($total, 2); ?></span>
                                </div>
                                
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="checkout.php" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Please <a href="login.php">login</a> to proceed with checkout.
                                    </div>
                                    <a href="login.php" class="btn btn-primary w-100">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </a>
                                <?php endif; ?>
                                
                                <div class="text-center mt-3">
                                    <a href="menu.php" class="text-decoration-none">
                                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(productId, change) {
            const quantityElement = event.target.parentElement.querySelector('span');
            let currentQuantity = parseInt(quantityElement.textContent);
            let newQuantity = currentQuantity + change;
            
            if (newQuantity < 1) {
                newQuantity = 1;
            }
            
            // Send AJAX request to update quantity
            const formData = new FormData();
            formData.append('action', 'update_quantity');
            formData.append('product_id', productId);
            formData.append('quantity', newQuantity);
            
            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html> 