<?php
// apis/inventory/adjust_stock.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$productId = isset($data['product_id']) ? intval($data['product_id']) : 0;
$qtyChange = isset($data['quantity_change']) ? intval($data['quantity_change']) : 0;
$movementId = isset($data['movement_id']) ? intval($data['movement_id']) : 1; // Default to Restock (1)
$notes = isset($data['notes']) ? trim($data['notes']) : '';

if ($productId <= 0 || $qtyChange === 0 || !in_array($movementId, [1, 3, 4])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters. Product ID, non-zero quantity change, and valid movement ID (1: Restock, 3: Adjustment, 4: Return) are required.'
    ]);
    exit;
}

try {
    // Verify product exists
    $prodStmt = $pdo->prepare("SELECT product_id FROM products WHERE product_id = ?");
    $prodStmt->execute([$productId]);
    if (!$prodStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        exit;
    }

    // Insert ledger entry
    $sql = "INSERT INTO inventory_ledger (product_id, movement_id, quantity_change, notes) 
            VALUES (:product_id, :movement_id, :quantity_change, :notes)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'product_id' => $productId,
        'movement_id' => $movementId,
        'quantity_change' => $qtyChange,
        'notes' => empty($notes) ? "Manual Inventory Adjustment" : $notes
    ]);

    // Update raw cost if provided
    $newRawCost = isset($data['new_raw_cost']) && $data['new_raw_cost'] !== null ? floatval($data['new_raw_cost']) : null;
    if ($newRawCost !== null) {
        $updateStmt = $pdo->prepare("UPDATE products SET raw_price = ? WHERE product_id = ?");
        $updateStmt->execute([$newRawCost, $productId]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock adjusted successfully'
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to adjust stock: ' . $e->getMessage()
    ]);
}
