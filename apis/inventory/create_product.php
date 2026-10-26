<?php
// apis/inventory/create_product.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$scentName = isset($data['scent_name']) ? trim($data['scent_name']) : '';
$categoryId = isset($data['category_id']) ? $data['category_id'] : '';
$newCategoryName = isset($data['new_category_name']) ? trim($data['new_category_name']) : '';
$sizeId = isset($data['size_id']) ? $data['size_id'] : '';
$newVolumeMl = isset($data['new_volume_ml']) ? intval($data['new_volume_ml']) : 0;
$targetAudience = isset($data['target_audience']) ? trim($data['target_audience']) : 'Unisex';

$retailPrice = isset($data['retail_price']) ? floatval($data['retail_price']) : 0.00;
$resellerPrice = isset($data['reseller_price']) ? floatval($data['reseller_price']) : 0.00;
$rawPrice = isset($data['raw_price']) ? floatval($data['raw_price']) : 0.00;
$commissionRetail   = isset($data['commission_retail'])   ? floatval($data['commission_retail'])   : null;
$commissionReseller = isset($data['commission_reseller']) ? floatval($data['commission_reseller']) : null;
// Legacy fallback: if old 'commission_amount' key sent (from UI), treat as retail
if ($commissionRetail === null && isset($data['commission_amount'])) {
    $commissionRetail   = floatval($data['commission_amount']);
    $commissionReseller = floatval($data['commission_amount']);
}
$commissionRetail   = $commissionRetail   ?? 0.00;
$commissionReseller = $commissionReseller ?? 0.00;
$initialStock = isset($data['initial_stock']) ? intval($data['initial_stock']) : 0;

if (empty($scentName) || $retailPrice <= 0 || $resellerPrice <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters. Scent name, retail price, and reseller price are required.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Resolve Scent
    $scentStmt = $pdo->prepare("SELECT scent_id FROM scents WHERE LOWER(scent_name) = LOWER(?) AND target_audience = ?");
    $scentStmt->execute([$scentName, $targetAudience]);
    $scentId = $scentStmt->fetchColumn();

    if ($scentId === false) {
        $insertScent = $pdo->prepare("INSERT INTO scents (scent_name, target_audience) VALUES (?, ?)");
        $insertScent->execute([$scentName, $targetAudience]);
        $scentId = $pdo->lastInsertId();
    }

    // 2. Resolve Category
    if ($categoryId === 'new' && !empty($newCategoryName)) {
        // Check if category name exists
        $catCheck = $pdo->prepare("SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER(?)");
        $catCheck->execute([$newCategoryName]);
        $categoryId = $catCheck->fetchColumn();

        if ($categoryId === false) {
            $insertCat = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $insertCat->execute([$newCategoryName]);
            $categoryId = $pdo->lastInsertId();
        }
    } else {
        $categoryId = intval($categoryId);
    }

    // 3. Resolve Size
    if ($sizeId === 'new' && $newVolumeMl > 0) {
        // Check if size volume exists
        $sizeCheck = $pdo->prepare("SELECT size_id FROM sizes WHERE volume_ml = ?");
        $sizeCheck->execute([$newVolumeMl]);
        $sizeId = $sizeCheck->fetchColumn();

        if ($sizeId === false) {
            $insertSize = $pdo->prepare("INSERT INTO sizes (volume_ml) VALUES (?)");
            $insertSize->execute([$newVolumeMl]);
            $sizeId = $pdo->lastInsertId();
        }
    } else {
        $sizeId = intval($sizeId);
    }

    if ($categoryId <= 0 || $sizeId <= 0) {
        throw new Exception("Please select or enter a valid category and bottle size.");
    }

    // 4. Check if duplicate product exists
    $prodCheck = $pdo->prepare("
        SELECT product_id FROM products 
        WHERE scent_id = ? AND category_id = ? AND size_id = ?
    ");
    $prodCheck->execute([$scentId, $categoryId, $sizeId]);
    if ($prodCheck->fetchColumn() !== false) {
        throw new Exception("A product matching this scent, category, size, and audience profile already exists.");
    }

    // 5. Insert Product
    // If commissions were left at 0, auto-inherit from existing product in same category+size
    if ($commissionRetail == 0 && $commissionReseller == 0) {
        $inheritStmt = $pdo->prepare("
            SELECT commission_retail, commission_reseller FROM products
            WHERE category_id = ? AND size_id = ? LIMIT 1
        ");
        $inheritStmt->execute([$categoryId, $sizeId]);
        $inherited = $inheritStmt->fetch();
        if ($inherited) {
            $commissionRetail   = floatval($inherited['commission_retail']);
            $commissionReseller = floatval($inherited['commission_reseller']);
        }
    }

    $prodSql = "
        INSERT INTO products (category_id, scent_id, size_id, raw_price, reseller_price, retail_price, commission_retail, commission_reseller)
        VALUES (:category_id, :scent_id, :size_id, :raw_price, :reseller_price, :retail_price, :commission_retail, :commission_reseller)
    ";
    $prodStmt = $pdo->prepare($prodSql);
    $prodStmt->execute([
        'category_id'         => $categoryId,
        'scent_id'            => $scentId,
        'size_id'             => $sizeId,
        'raw_price'           => $rawPrice,
        'reseller_price'      => $resellerPrice,
        'retail_price'        => $retailPrice,
        'commission_retail'   => $commissionRetail,
        'commission_reseller' => $commissionReseller
    ]);
    $productId = $pdo->lastInsertId();

    // 6. Log Stock Ledger
    if ($initialStock > 0) {
        $ledgerSql = "
            INSERT INTO inventory_ledger (product_id, movement_id, quantity_change, notes)
            VALUES (:product_id, 1, :quantity_change, 'Initial stock seed during product creation')
        ";
        $ledgerStmt = $pdo->prepare($ledgerSql);
        $ledgerStmt->execute([
            'product_id' => $productId,
            'quantity_change' => $initialStock
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Product registered successfully.',
        'data' => [
            'product_id' => $productId
        ]
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
