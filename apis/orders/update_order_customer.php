<?php
// apis/orders/update_order_customer.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$orderId = isset($data['order_id']) ? intval($data['order_id']) : 0;
$customerId = isset($data['customer_id']) ? intval($data['customer_id']) : 0;
$newCustomerName = isset($data['new_customer_name']) ? trim($data['new_customer_name']) : '';

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order ID.'
    ]);
    exit;
}

try {
    $targetCustomerId = 0;

    // 1. If a new customer name was typed
    if (!empty($newCustomerName)) {
        // Check if customer with this exact name already exists
        $checkStmt = $pdo->prepare("SELECT customer_id FROM customers WHERE LOWER(customer_name) = LOWER(?) LIMIT 1");
        $checkStmt->execute([$newCustomerName]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            $targetCustomerId = intval($existing['customer_id']);
        } else {
            // Create a new customer
            $insStmt = $pdo->prepare("INSERT INTO customers (customer_name, customer_type) VALUES (?, 'Retailer')");
            $insStmt->execute([$newCustomerName]);
            $targetCustomerId = intval($pdo->lastInsertId());
        }
    } else if ($customerId > 0) {
        $targetCustomerId = $customerId;
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Please select or enter a valid customer.'
        ]);
        exit;
    }

    // 2. Verify target customer exists
    $custStmt = $pdo->prepare("SELECT customer_id, customer_name, contact_number, email, customer_type FROM customers WHERE customer_id = ?");
    $custStmt->execute([$targetCustomerId]);
    $customerInfo = $custStmt->fetch();

    if (!$customerInfo) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Selected customer not found.'
        ]);
        exit;
    }

    // 3. Update orders table ONLY for this specific order
    $updStmt = $pdo->prepare("UPDATE orders SET customer_id = :customer_id WHERE order_id = :order_id");
    $updStmt->execute([
        'customer_id' => $targetCustomerId,
        'order_id' => $orderId
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Order customer updated successfully.',
        'data' => $customerInfo
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
