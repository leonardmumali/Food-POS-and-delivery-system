<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    redirect('login.php');
}

$success = '';
$error = '';

// Handle delivery zone actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = sanitize_input($_POST['name']);
                $delivery_fee = (float)$_POST['delivery_fee'];
                $estimated_time = sanitize_input($_POST['estimated_time']);
                
                $stmt = $pdo->prepare("INSERT INTO delivery_zones (name, delivery_fee, estimated_time) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $delivery_fee, $estimated_time])) {
                    $success = 'Delivery zone added successfully!';
                } else {
                    $error = 'Failed to add delivery zone.';
                }
                break;
                
            case 'update':
                $id = (int)$_POST['id'];
                $name = sanitize_input($_POST['name']);
                $delivery_fee = (float)$_POST['delivery_fee'];
                $estimated_time = sanitize_input($_POST['estimated_time']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                $stmt = $pdo->prepare("UPDATE delivery_zones SET name = ?, delivery_fee = ?, estimated_time = ?, is_active = ? WHERE id = ?");
                if ($stmt->execute([$name, $delivery_fee, $estimated_time, $is_active, $id])) {
                    $success = 'Delivery zone updated successfully!';
                } else {
                    $error = 'Failed to update delivery zone.';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("DELETE FROM delivery_zones WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $success = 'Delivery zone deleted successfully!';
                } else {
                    $error = 'Failed to delete delivery zone.';
                }
                break;
        }
    }
}

// Get delivery zones
$stmt = $pdo->query("SELECT * FROM delivery_zones ORDER BY name");
$delivery_zones = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Delivery Zones - FoodExpress Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }
        .admin-sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-radius: 0;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #34495e;
            color: #fff;
        }
        .admin-sidebar .nav-link i {
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="admin-sidebar">
                    <div class="p-3 border-bottom border-secondary">
                        <h5 class="text-white mb-0">
                            <i class="fas fa-utensils me-2"></i>FoodExpress Admin
                        </h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box me-2"></i>Products
                        </a>
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-cart me-2"></i>Orders
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link active" href="delivery_zones.php">
                            <i class="fas fa-map-marker-alt me-2"></i>Delivery Zones
                        </a>
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr class="text-secondary">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-4 py-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Manage Delivery Zones</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                        <i class="fas fa-plus me-2"></i>Add New Zone
                    </button>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Delivery Zones Table -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($delivery_zones)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                <h4>No Delivery Zones</h4>
                                <p class="text-muted">Add delivery zones to configure delivery areas and fees.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Zone Name</th>
                                            <th>Delivery Fee</th>
                                            <th>Estimated Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($delivery_zones as $zone): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($zone['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <strong>KSh <?php echo number_format($zone['delivery_fee'], 2); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($zone['estimated_time']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $zone['is_active'] ? 'success' : 'danger'; ?>">
                                                        <?php echo $zone['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editZone(<?php echo htmlspecialchars(json_encode($zone)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteZone(<?php echo $zone['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Zone Modal -->
    <div class="modal fade" id="addZoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Delivery Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Zone Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_fee" class="form-label">Delivery Fee (KSh) *</label>
                            <input type="number" class="form-control" id="delivery_fee" name="delivery_fee" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="estimated_time" class="form-label">Estimated Delivery Time *</label>
                            <input type="text" class="form-control" id="estimated_time" name="estimated_time" placeholder="e.g., 20-30 minutes" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Zone Modal -->
    <div class="modal fade" id="editZoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Delivery Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Zone Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_delivery_fee" class="form-label">Delivery Fee (KSh) *</label>
                            <input type="number" class="form-control" id="edit_delivery_fee" name="delivery_fee" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_estimated_time" class="form-label">Estimated Delivery Time *</label>
                            <input type="text" class="form-control" id="edit_estimated_time" name="estimated_time" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                <label class="form-check-label" for="edit_is_active">
                                    Active Zone
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteZoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this delivery zone? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_zone_id">
                        <button type="submit" class="btn btn-danger">Delete Zone</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editZone(zone) {
            document.getElementById('edit_id').value = zone.id;
            document.getElementById('edit_name').value = zone.name;
            document.getElementById('edit_delivery_fee').value = zone.delivery_fee;
            document.getElementById('edit_estimated_time').value = zone.estimated_time;
            document.getElementById('edit_is_active').checked = zone.is_active == 1;
            
            new bootstrap.Modal(document.getElementById('editZoneModal')).show();
        }
        
        function deleteZone(id) {
            document.getElementById('delete_zone_id').value = id;
            new bootstrap.Modal(document.getElementById('deleteZoneModal')).show();
        }
    </script>
</body>
</html> 