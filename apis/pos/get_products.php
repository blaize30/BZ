<?php
// apis/pos/get_products.php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    $sql = "SELECT 
                p.product_id,
                p.category_id,
                c.category_name,
                p.size_id,
                sz.volume_ml,
                p.scent_id,
                s.scent_name,
                s.target_audience,
                p.raw_price,
                p.reseller_price,
                p.retail_price,
                p.commission_retail,
                p.commission_reseller,
                COALESCE(stock_query.stock_qty, 0) AS stock
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            JOIN sizes sz ON p.size_id = sz.size_id
            JOIN scents s ON p.scent_id = s.scent_id
            LEFT JOIN (
                SELECT product_id, SUM(quantity_change) as stock_qty
                FROM inventory_ledger
                GROUP BY product_id
            ) AS stock_query ON p.product_id = stock_query.product_id
            ORDER BY s.scent_name ASC, sz.volume_ml ASC";

    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}
