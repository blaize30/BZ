<?php
// apis/orders/pay_order.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
$paymentStatus = isset($data['payment_status']) ? trim($data['payment_status']) : 'Paid';
$paymentMode = isset($data['payment_mode']) ? trim($data['payment_mode']) : '';

if ($orderId <= 0 || !in_array($paymentStatus, ['Paid', 'Unpaid'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order ID or payment status parameter.'
    ]);
    exit;
}

if ($paymentStatus === 'Paid' && !in_array($paymentMode, ['Cash', 'GCash', 'Bank Transfer', 'Other'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Valid payment mode is required when marking order as Paid.'
    ]);
    exit;
}

try {
    // Check if order exists
    $checkStmt = $pdo->prepare("SELECT order_id FROM orders WHERE order_id = ?");
    $checkStmt->execute([$orderId]);
    $order = $checkStmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        exit;
    }

    if ($paymentStatus === 'Paid') {
        // Calculate grand total of the order
        $totalStmt = $pdo->prepare("SELECT COALESCE(SUM(quantity * price_applied), 0) FROM order_items WHERE order_id = ?");
        $totalStmt->execute([$orderId]);
        $grandTotal = floatval($totalStmt->fetchColumn());

        // Delete existing payment logs
        $delStmt = $pdo->prepare("DELETE FROM order_payments WHERE order_id = ?");
        $delStmt->execute([$orderId]);

        // Insert single full payment log
        $insStmt = $pdo->prepare("INSERT INTO order_payments (order_id, amount_paid, payment_mode, payment_timestamp) VALUES (?, ?, ?, NOW())");
        $insStmt->execute([$orderId, $grandTotal, $paymentMode]);

        // Update orders
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET payment_status = 'Paid', 
                payment_mode = :payment_mode, 
                payment_timestamp = CURRENT_TIMESTAMP(),
                amount_paid = :amount_paid
            WHERE order_id = :order_id
        ");
        $stmt->execute([
            'payment_mode' => $paymentMode,
            'amount_paid' => $grandTotal,
            'order_id' => $orderId
        ]);
    } else {
        // Delete existing payment logs
        $delStmt = $pdo->prepare("DELETE FROM order_payments WHERE order_id = ?");
        $delStmt->execute([$orderId]);

        // Update orders
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET payment_status = 'Unpaid', 
                payment_timestamp = NULL,
                amount_paid = 0.00
            WHERE order_id = :order_id
        ");
        $stmt->execute([
            'order_id' => $orderId
        ]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Order payment status updated successfully.'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
