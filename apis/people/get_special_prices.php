<?php
// apis/people/get_special_prices.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

if ($customerId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid customer ID']);
    exit;
}

try {
    // 1. Fetch customer details
    $custStmt = $pdo->prepare("SELECT customer_id, customer_name, customer_type FROM customers WHERE customer_id = ?");
    $custStmt->execute([$customerId]);
    $customer = $custStmt->fetch();
    
    if (!$customer) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
        exit;
    }

    // 2. Fetch special prices
    $priceStmt = $pdo->prepare("
        SELECT csp.category_id, csp.size_id, csp.special_price, sz.volume_ml, c.category_name 
        FROM customer_special_prices csp
        JOIN categories c ON csp.category_id = c.category_id
        JOIN sizes sz ON csp.size_id = sz.size_id
        WHERE csp.customer_id = ?
        ORDER BY c.category_name ASC, sz.volume_ml ASC
    ");
    $priceStmt->execute([$customerId]);
    $prices = $priceStmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'customer_id' => $customer['customer_id'],
            'customer_name' => $customer['customer_name'],
            'customer_type' => $customer['customer_type'],
            'prices' => $prices
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
