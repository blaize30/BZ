<?php
// apis/people/update_rep.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$repId = isset($data['rep_id']) ? intval($data['rep_id']) : 0;
$repName = isset($data['rep_name']) ? trim($data['rep_name']) : '';

if ($repId <= 0 || empty($repName)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters. Representative ID and Name are required.'
    ]);
    exit;
}

try {
    // Check if duplicate representative name exists
    $checkStmt = $pdo->prepare("SELECT rep_id FROM sales_representatives WHERE LOWER(rep_name) = LOWER(?) AND rep_id != ?");
    $checkStmt->execute([$repName, $repId]);
    if ($checkStmt->fetchColumn() !== false) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'A representative with this name already exists.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE sales_representatives SET rep_name = ? WHERE rep_id = ?");
    $stmt->execute([$repName, $repId]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Representative renamed successfully.'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
