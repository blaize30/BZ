<?php
// apis/orders/add_payment.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
$amountPaid = isset($data['amount_paid']) ? floatval($data['amount_paid']) : 0.00;
$paymentMode = isset($data['payment_mode']) ? trim($data['payment_mode']) : '';

if ($orderId <= 0 || $amountPaid <= 0 || !in_array($paymentMode, ['Cash', 'GCash', 'Bank Transfer', 'Other'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters. Order ID, positive payment amount, and valid payment mode are required.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Fetch grand total of the order
    $totalStmt = $pdo->prepare("SELECT COALESCE(SUM(quantity * price_applied), 0) FROM order_items WHERE order_id = ?");
    $totalStmt->execute([$orderId]);
    $grandTotal = floatval($totalStmt->fetchColumn());

    if ($grandTotal <= 0) {
        throw new Exception("Order not found or has no items");
    }

    // 2. Fetch current total paid
    $paidStmt = $pdo->prepare("SELECT COALESCE(SUM(amount_paid), 0) FROM order_payments WHERE order_id = ?");
    $paidStmt->execute([$orderId]);
    $currentTotalPaid = floatval($paidStmt->fetchColumn());

    // Prevent paying more than grand total (cap the payment)
    $remaining = $grandTotal - $currentTotalPaid;
    if ($amountPaid > $remaining + 0.01) { // allow small float delta
        $amountPaid = $remaining;
    }

    if ($amountPaid <= 0) {
        throw new Exception("Order is already fully paid.");
    }

    // 3. Log the new payment
    $insStmt = $pdo->prepare("INSERT INTO order_payments (order_id, amount_paid, payment_mode, payment_timestamp) VALUES (?, ?, ?, NOW())");
    $insStmt->execute([$orderId, $amountPaid, $paymentMode]);

    // 4. Recalculate new total paid
    $newTotalPaid = $currentTotalPaid + $amountPaid;

    // 5. Determine new payment status
    $newStatus = 'Partial';
    if ($newTotalPaid >= $grandTotal - 0.01) {
        $newStatus = 'Paid';
        $newTotalPaid = $grandTotal; // clean up float rounding
    }

    // 6. Update orders table
    $updStmt = $pdo->prepare("
        UPDATE orders 
        SET amount_paid = :amount_paid,
            payment_status = :payment_status,
            payment_timestamp = NOW()
        WHERE order_id = :order_id
    ");
    $updStmt->execute([
        'amount_paid' => $newTotalPaid,
        'payment_status' => $newStatus,
        'order_id' => $orderId
    ]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Payment logged successfully',
        'data' => [
            'amount_paid' => $newTotalPaid,
            'payment_status' => $newStatus
        ]
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
