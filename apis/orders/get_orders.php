<?php
// apis/orders/get_orders.php
header('Content-Type: application/json');
require_once '../../config/db.php';

try {
    if (isset($_GET['order_id'])) {
        // Fetch detailed order items
        $orderId = intval($_GET['order_id']);
        
        // 1. Fetch Order Metadata
        $metaStmt = $pdo->prepare("
            SELECT 
                o.order_id,
                o.order_timestamp,
                o.delivery_status,
                o.payment_mode,
                o.payment_status,
                o.payment_timestamp,
                o.amount_paid,
                o.commission_status,
                o.commission_timestamp,
                o.discount,
                c.customer_id,
                c.customer_name,
                c.contact_number,
                c.email,
                c.customer_type,
                sr.rep_name
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN sales_representatives sr ON o.rep_id = sr.rep_id
            WHERE o.order_id = ?
        ");
        $metaStmt->execute([$orderId]);
        $orderMeta = $metaStmt->fetch();
        
        if (!$orderMeta) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Order not found']);
            exit;
        }

        // 2. Fetch Order Items
        $itemsStmt = $pdo->prepare("
            SELECT 
                oi.order_item_id,
                oi.product_id,
                oi.quantity,
                oi.raw_cost_applied,
                oi.price_applied,
                oi.commission_applied,
                c.category_name,
                s.scent_name,
                sz.volume_ml,
                s.target_audience
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            JOIN categories c ON p.category_id = c.category_id
            JOIN scents s ON p.scent_id = s.scent_id
            JOIN sizes sz ON p.size_id = sz.size_id
            WHERE oi.order_id = ?
            ORDER BY s.scent_name ASC
        ");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll();

        // 3. Fetch Order Payments
        $payStmt = $pdo->prepare("SELECT payment_id, amount_paid, payment_mode, payment_timestamp FROM order_payments WHERE order_id = ? ORDER BY payment_timestamp ASC");
        $payStmt->execute([$orderId]);
        $payments = $payStmt->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data' => [
                'order' => $orderMeta,
                'items' => $items,
                'payments' => $payments
            ]
        ]);
    } else {
        // Fetch list of all orders
        $sql = "
            SELECT 
                o.order_id,
                o.order_timestamp,
                o.delivery_status,
                o.payment_mode,
                o.payment_status,
                o.payment_timestamp,
                o.amount_paid,
                o.commission_status,
                o.commission_timestamp,
                o.discount,
                c.customer_name,
                sr.rep_name,
                COALESCE(items_query.total_amount, 0)     AS total_amount,
                COALESCE(items_query.total_qty, 0)        AS total_items,
                COALESCE(items_query.total_commission, 0) AS total_commission,
                COALESCE(items_query.total_raw_cost, 0)   AS total_raw_cost,
                items_query.item_sizes
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN sales_representatives sr ON o.rep_id = sr.rep_id
            LEFT JOIN (
                SELECT 
                    oi.order_id, 
                    SUM(oi.quantity * oi.price_applied)      AS total_amount,
                    SUM(oi.quantity * oi.commission_applied) AS total_commission,
                    SUM(oi.quantity * oi.raw_cost_applied)   AS total_raw_cost,
                    SUM(oi.quantity)                      AS total_qty,
                    GROUP_CONCAT(DISTINCT sz.volume_ml)   AS item_sizes
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                JOIN sizes sz ON p.size_id = sz.size_id
                GROUP BY oi.order_id
            ) AS items_query ON o.order_id = items_query.order_id
            ORDER BY o.order_timestamp DESC
        ";
        
        $stmt = $pdo->query($sql);
        $orders = $stmt->fetchAll();

        echo json_encode([
            'status' => 'success',
            'data' => $orders
        ]);
    }
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
