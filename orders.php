<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php?redirect=orders.php');
}

// Get user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - FoodExpress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Orders Section -->
    <section class="py-5" style="margin-top: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-5 fw-bold mb-4">My Orders</h1>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="row">
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h3>No orders yet</h3>
                        <p class="text-muted">You haven't placed any orders yet. Start by browsing our menu!</p>
                        <a href="menu.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-utensils me-2"></i>Browse Menu
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($orders as $order): ?>
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <h6 class="mb-1">Order #<?php echo htmlspecialchars($order['order_number']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucwords(str_replace('_', ' ', $order['status'])); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </div>
                                        <div class="col-md-2">
                                            <strong>KSh <?php echo number_format($order['total_amount'], 2); ?></strong>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <button class="btn btn-outline-danger btn-sm ms-2" onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                // Send AJAX request to cancel order
                fetch('ajax/cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to cancel order: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong');
                });
            }
        }
    </script>
</body>
</html> 