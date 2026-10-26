<?php
// apis/people/update_customer_type.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$customerId = isset($data['customer_id']) ? intval($data['customer_id']) : 0;
$customerType = isset($data['customer_type']) ? trim($data['customer_type']) : '';

if ($customerId <= 0 || !in_array($customerType, ['Retailer', 'Reseller', 'Special'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid customer ID or customer type parameter.'
    ]);
    exit;
}

try {
    // Check if customer exists
    $checkStmt = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_id = ?");
    $checkStmt->execute([$customerId]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
        exit;
    }

    // Update customer type
    $stmt = $pdo->prepare("UPDATE customers SET customer_type = ? WHERE customer_id = ?");
    $stmt->execute([$customerType, $customerId]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Customer account type updated successfully.'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
