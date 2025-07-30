<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product_id']) || !isset($input['product_name']) || !isset($input['product_price'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$product_id = (int)$input['product_id'];
$product_name = $input['product_name'];
$product_price = (float)$input['product_price'];
$quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        $item['quantity'] += $quantity;
        $item['total_price'] = $item['quantity'] * $item['unit_price'];
        $found = true;
        break;
    }
}

// If not found, add new item
if (!$found) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'product_name' => $product_name,
        'unit_price' => $product_price,
        'quantity' => $quantity,
        'total_price' => $product_price * $quantity
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'Item added to cart successfully',
    'cart_count' => count($_SESSION['cart'])
]);
?> 