<?php
// apis/pos/create_rep.php
header('Content-Type: application/json');
require_once '../../config/db.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$name = isset($data['rep_name']) ? trim($data['rep_name']) : '';

if (empty($name)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Representative name is required'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO sales_representatives (rep_name) VALUES (:name)");
    $stmt->execute(['name' => $name]);
    $newId = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'message' => 'Representative created successfully',
        'data' => [
            'rep_id' => $newId,
            'rep_name' => $name
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create representative: ' . $e->getMessage()
    ]);
}
