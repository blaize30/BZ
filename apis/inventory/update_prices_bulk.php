<?php
// apis/inventory/update_prices_bulk.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$categoryId = isset($data['category_id']) ? intval($data['category_id']) : 0;
$sizeId = isset($data['size_id']) ? intval($data['size_id']) : 0;
$retailPrice = isset($data['retail_price']) ? floatval($data['retail_price']) : 0.00;
$resellerPrice = isset($data['reseller_price']) ? floatval($data['reseller_price']) : 0.00;
$rawPrice = isset($data['raw_price']) ? floatval($data['raw_price']) : -1.00;
$commRetail = isset($data['commission_retail']) ? floatval($data['commission_retail']) : -1.00;
$commReseller = isset($data['commission_reseller']) ? floatval($data['commission_reseller']) : -1.00;

if ($categoryId <= 0 || $sizeId <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters. Category ID and Size ID are required.'
    ]);
    exit;
}

if ($retailPrice <= 0 && $resellerPrice <= 0 && $rawPrice < 0 && $commRetail < 0 && $commReseller < 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please enter at least one valid adjustment value.'
    ]);
    exit;
}

try {
    $updates = [];
    $params = [
        'category_id' => $categoryId,
        'size_id' => $sizeId
    ];

    if ($retailPrice > 0) {
        $updates[] = "retail_price = :retail_price";
        $params['retail_price'] = $retailPrice;
    }
    if ($resellerPrice > 0) {
        $updates[] = "reseller_price = :reseller_price";
        $params['reseller_price'] = $resellerPrice;
    }
    if ($rawPrice >= 0) {
        $updates[] = "raw_price = :raw_price";
        $params['raw_price'] = $rawPrice;
    }
    if ($commRetail >= 0) {
        $updates[] = "commission_retail = :commission_retail";
        $params['commission_retail'] = $commRetail;
    }
    if ($commReseller >= 0) {
        $updates[] = "commission_reseller = :commission_reseller";
        $params['commission_reseller'] = $commReseller;
    }

    $sql = "UPDATE products SET " . implode(", ", $updates) . " WHERE category_id = :category_id AND size_id = :size_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $count = $stmt->rowCount();

    echo json_encode([
        'status' => 'success',
        'message' => "Successfully updated pricing for $count products."
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
