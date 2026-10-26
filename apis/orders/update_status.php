<?php
// apis/orders/update_status.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
$status = isset($data['delivery_status']) ? trim($data['delivery_status']) : '';

if ($orderId <= 0 || !in_array($status, ['Preparing', 'Delivered'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order ID or delivery status. Status must be "Preparing" or "Delivered".'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET delivery_status = :status WHERE order_id = :order_id");
    $stmt->execute([
        'status' => $status,
        'order_id' => $orderId
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Delivery status updated successfully'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update order status: ' . $e->getMessage()
    ]);
}
