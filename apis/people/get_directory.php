<?php
// apis/people/get_directory.php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    // 1. Fetch Sales Representatives with calculated stats (total sales & commissions)
    $repsSql = "
        SELECT 
            sr.rep_id,
            sr.rep_name,
            COALESCE(SUM(oi.quantity), 0) AS total_units,
            COALESCE(SUM(oi.quantity * oi.price_applied), 0) AS total_sales,
            COALESCE(SUM(oi.quantity * oi.commission_applied), 0) AS total_commission
        FROM sales_representatives sr
        LEFT JOIN orders o ON sr.rep_id = o.rep_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY sr.rep_id
        ORDER BY total_sales DESC
    ";
    $repsStmt = $pdo->query($repsSql);
    $reps = $repsStmt->fetchAll();

    // 2. Fetch Customers with calculated stats (total spent & total units)
    $custSql = "
        SELECT 
            c.customer_id,
            c.customer_name,
            c.contact_number,
            c.email,
            c.customer_type,
            COALESCE(SUM(oi.quantity * oi.price_applied), 0) AS total_spent,
            COALESCE(SUM(oi.quantity), 0) AS total_units
        FROM customers c
        LEFT JOIN orders o ON c.customer_id = o.customer_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY c.customer_id
        ORDER BY total_spent DESC
    ";
    $custStmt = $pdo->query($custSql);
    $customers = $custStmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'reps' => $reps,
            'customers' => $customers
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
