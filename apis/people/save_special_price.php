<?php
// apis/people/save_special_price.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$customerId = isset($data['customer_id']) ? intval($data['customer_id']) : 0;
$categoryId = isset($data['category_id']) ? intval($data['category_id']) : 0;
$sizeId = isset($data['size_id']) ? intval($data['size_id']) : 0;
$specialPrice = isset($data['special_price']) ? floatval($data['special_price']) : null;
$action = isset($data['action']) ? trim($data['action']) : 'save'; // 'save' or 'delete'

if ($customerId <= 0 || $categoryId <= 0 || $sizeId <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Customer ID, Category ID and Size ID are required.']);
    exit;
}

try {
    if ($action === 'delete' || ($specialPrice !== null && $specialPrice <= 0)) {
        // Delete override
        $stmt = $pdo->prepare("DELETE FROM customer_special_prices WHERE customer_id = ? AND category_id = ? AND size_id = ?");
        $stmt->execute([$customerId, $categoryId, $sizeId]);
        echo json_encode(['status' => 'success', 'message' => 'Special price override deleted.']);
    } else {
        if ($specialPrice === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Special price must be specified to save.']);
            exit;
        }
        // Save override
        $stmt = $pdo->prepare("
            INSERT INTO customer_special_prices (customer_id, category_id, size_id, special_price)
            VALUES (:customer_id, :category_id, :size_id, :special_price)
            ON DUPLICATE KEY UPDATE special_price = :special_price_update
        ");
        $stmt->execute([
            'customer_id' => $customerId,
            'category_id' => $categoryId,
            'size_id' => $sizeId,
            'special_price' => $specialPrice,
            'special_price_update' => $specialPrice
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Special price override saved successfully.']);
    }
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
