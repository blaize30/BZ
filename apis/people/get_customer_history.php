<?php
// apis/people/get_customer_history.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

if ($customerId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid customer ID']);
    exit;
}

try {
    // Fetch customer details
    $custStmt = $pdo->prepare("SELECT customer_id, customer_name, customer_type FROM customers WHERE customer_id = ?");
    $custStmt->execute([$customerId]);
    $customer = $custStmt->fetch();

    if (!$customer) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
        exit;
    }

    // Fetch order list for this customer
    $ordersSql = "
        SELECT 
            o.order_id,
            o.order_timestamp,
            o.payment_mode,
            o.payment_status,
            o.delivery_status,
            sr.rep_name,
            COALESCE(SUM(oi.quantity), 0) AS total_items,
            COALESCE(SUM(oi.quantity * oi.price_applied), 0) AS total_amount
        FROM orders o
        JOIN sales_representatives sr ON o.rep_id = sr.rep_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.customer_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_timestamp DESC
    ";
    
    $ordersStmt = $pdo->prepare($ordersSql);
    $ordersStmt->execute([$customerId]);
    $orders = $ordersStmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'customer' => $customer,
            'orders' => $orders
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
