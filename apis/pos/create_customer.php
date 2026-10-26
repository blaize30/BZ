<?php
// apis/pos/create_customer.php
header('Content-Type: application/json');
require_once '../../config/db.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$name = isset($data['customer_name']) ? trim($data['customer_name']) : '';
$contact = isset($data['contact_number']) ? trim($data['contact_number']) : null;
$email = isset($data['email']) ? trim($data['email']) : null;
$type = isset($data['customer_type']) ? trim($data['customer_type']) : 'Retailer';

if (empty($name)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Customer name is required'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO customers (customer_name, contact_number, email, customer_type) VALUES (:name, :contact, :email, :type)");
    $stmt->execute([
        'name' => $name,
        'contact' => $contact,
        'email' => $email,
        'type' => $type
    ]);
    $newId = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'message' => 'Customer created successfully',
        'data' => [
            'customer_id' => $newId,
            'customer_name' => $name,
            'contact_number' => $contact,
            'email' => $email,
            'customer_type' => $type
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create customer: ' . $e->getMessage()
    ]);
}
