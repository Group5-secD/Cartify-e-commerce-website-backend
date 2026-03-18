<?php
require_once __DIR__ . '/../../config/cors.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../auth/middleware.php';

requireLogin();

header('Content-Type: application/json');

$user_id = getLoggedInUserId();
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$product_id = (int)$data['product_id'];
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

try {
    // Check if item already exists in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing_item = $stmt->fetch();

    if ($existing_item) {
        // Update quantity
        $new_quantity = $existing_item['quantity'] + $quantity;
        $update_stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update_stmt->execute([$new_quantity, $existing_item['id']]);
        echo json_encode(['message' => 'Cart updated', 'quantity' => $new_quantity]);
    } else {
        // Insert new item
        $insert_stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->execute([$user_id, $product_id, $quantity]);
        echo json_encode(['message' => 'Item added to cart']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
