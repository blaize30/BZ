<?php
// apis/people/update_customer.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$customerId = isset($data['customer_id']) ? intval($data['customer_id']) : 0;
$customerName = isset($data['customer_name']) ? trim($data['customer_name']) : '';
$contactNumber = isset($data['contact_number']) ? trim($data['contact_number']) : null;
$email = isset($data['email']) ? trim($data['email']) : null;
$customerType = isset($data['customer_type']) ? trim($data['customer_type']) : 'Retailer';

if ($customerId <= 0 || empty($customerName)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters. Customer ID and Name are required.'
    ]);
    exit;
}

if (!in_array($customerType, ['Retailer', 'Reseller', 'Special'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid customer type.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE customers 
        SET customer_name = :customer_name,
            contact_number = :contact_number,
            email = :email,
            customer_type = :customer_type
        WHERE customer_id = :customer_id
    ");
    
    $stmt->execute([
        'customer_name' => $customerName,
        'contact_number' => empty($contactNumber) ? null : $contactNumber,
        'email' => empty($email) ? null : $email,
        'customer_type' => $customerType,
        'customer_id' => $customerId
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Customer details updated successfully.'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
