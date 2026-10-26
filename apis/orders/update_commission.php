<?php
// apis/orders/update_commission.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
$commissionStatus = isset($data['commission_status']) ? trim($data['commission_status']) : '';

if ($orderId <= 0 || !in_array($commissionStatus, ['Paid', 'Unpaid'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order ID or commission status. Status must be "Paid" or "Unpaid".'
    ]);
    exit;
}

try {
    if ($commissionStatus === 'Paid') {
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET commission_status = 'Paid', 
                commission_timestamp = CURRENT_TIMESTAMP() 
            WHERE order_id = :order_id
        ");
    } else {
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET commission_status = 'Unpaid', 
                commission_timestamp = NULL 
            WHERE order_id = :order_id
        ");
    }
    $stmt->execute(['order_id' => $orderId]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Commission status updated successfully'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update commission status: ' . $e->getMessage()
    ]);
}
