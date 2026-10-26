<?php
// apis/inventory/get_ledger.php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    $sql = "SELECT 
                il.ledger_id,
                il.product_id,
                il.quantity_change,
                il.timestamp,
                il.order_id,
                il.notes,
                mt.movement_name,
                s.scent_name,
                sz.volume_ml,
                c.category_name
            FROM inventory_ledger il
            JOIN movement_types mt ON il.movement_id = mt.movement_id
            JOIN products p ON il.product_id = p.product_id
            JOIN scents s ON p.scent_id = s.scent_id
            JOIN sizes sz ON p.size_id = sz.size_id
            JOIN categories c ON p.category_id = c.category_id
            ORDER BY il.timestamp DESC, il.ledger_id DESC";

    $stmt = $pdo->query($sql);
    $ledger = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $ledger
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch ledger logs: ' . $e->getMessage()
    ]);
}
