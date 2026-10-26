<?php
// apis/pos/get_reps.php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    $stmt = $pdo->query("SELECT rep_id, rep_name FROM sales_representatives ORDER BY rep_name ASC");
    $reps = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $reps
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch sales representatives: ' . $e->getMessage()
    ]);
}
