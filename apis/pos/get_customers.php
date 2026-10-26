<?php
// apis/pos/get_customers.php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    $stmt = $pdo->query("SELECT customer_id, customer_name, contact_number, email, customer_type FROM customers ORDER BY customer_name ASC");
    $customers = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $customers
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch customers: ' . $e->getMessage()
    ]);
}
