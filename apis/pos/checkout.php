<?php
// apis/pos/checkout.php
header('Content-Type: application/json');
require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$repId = isset($data['rep_id']) ? intval($data['rep_id']) : 0;
$customerId = isset($data['customer_id']) ? intval($data['customer_id']) : 0;
$paymentMode = isset($data['payment_mode']) ? trim($data['payment_mode']) : '';
$items = isset($data['items']) ? $data['items'] : [];
$discount = isset($data['discount']) ? floatval($data['discount']) : 0.00;
$paymentStatus = isset($data['payment_status']) ? trim($data['payment_status']) : 'Paid';

if ($repId <= 0 || $customerId <= 0 || empty($paymentMode) || empty($items)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid checkout request parameters. Sales representative, customer, payment mode, and items are required.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Create Order
    $orderSql = "INSERT INTO orders (rep_id, customer_id, delivery_status, payment_mode, payment_status, payment_timestamp, discount) 
                 VALUES (:rep_id, :customer_id, 'Preparing', :payment_mode, :payment_status, :payment_timestamp, :discount)";
    $orderStmt = $pdo->prepare($orderSql);
    $orderStmt->execute([
        'rep_id' => $repId,
        'customer_id' => $customerId,
        'payment_mode' => $paymentMode,
        'payment_status' => $paymentStatus,
        'payment_timestamp' => ($paymentStatus === 'Paid' ? date('Y-m-d H:i:s') : null),
        'discount' => $discount
    ]);
    $orderId = $pdo->lastInsertId();

    // Calculate total order value to distribute discount if any
    $totalValueBeforeDiscount = 0.00;
    $productDetails = [];

    // Fetch details of all products in cart to know their prices and commission
    foreach ($items as $item) {
        $pId = intval($item['product_id']);
        $qty = intval($item['quantity']);
        if ($qty <= 0) {
            throw new Exception("Quantity must be greater than 0");
        }

        $prodStmt = $pdo->prepare("SELECT product_id, category_id, size_id, raw_price, reseller_price, retail_price, commission_retail, commission_reseller FROM products WHERE product_id = ?");
        $prodStmt->execute([$pId]);
        $product = $prodStmt->fetch();
        if (!$product) {
            throw new Exception("Product ID $pId not found");
        }

        // Check if there is enough stock before checkout
        $stockStmt = $pdo->prepare("SELECT COALESCE(SUM(quantity_change), 0) as stock_qty FROM inventory_ledger WHERE product_id = ?");
        $stockStmt->execute([$pId]);
        $currentStock = intval($stockStmt->fetchColumn());
        if ($currentStock < $qty) {
            // Get product name for clear error message
            $scentStmt = $pdo->prepare("SELECT s.scent_name, sz.volume_ml FROM products p JOIN scents s ON p.scent_id = s.scent_id JOIN sizes sz ON p.size_id = sz.size_id WHERE p.product_id = ?");
            $scentStmt->execute([$pId]);
            $scentInfo = $scentStmt->fetch();
            $pName = ($scentInfo) ? "{$scentInfo['scent_name']} ({$scentInfo['volume_ml']}ml)" : "Product ID $pId";
            throw new Exception("Insufficient stock for $pName. Available stock: $currentStock, requested: $qty.");
        }

        // Determine price applied (based on POS client setting)
        $pricingMode = isset($data['pricing_mode']) ? $data['pricing_mode'] : 'retail';
        if ($pricingMode === 'special') {
            $spStmt = $pdo->prepare("SELECT special_price FROM customer_special_prices WHERE customer_id = ? AND category_id = ? AND size_id = ?");
            $spStmt->execute([$customerId, $product['category_id'], $product['size_id']]);
            $spPrice = $spStmt->fetchColumn();
            if ($spPrice !== false) {
                $unitPrice = floatval($spPrice);
            } else {
                $unitPrice = floatval($product['retail_price']); // fallback to retail
            }
        } else {
            $unitPrice = ($pricingMode === 'reseller') ? floatval($product['reseller_price']) : floatval($product['retail_price']);
        }
        
        $itemTotal = $unitPrice * $qty;
        $totalValueBeforeDiscount += $itemTotal;

        $productDetails[] = [
            'product_id' => $pId,
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'raw_price' => floatval($product['raw_price']),
            'commission' => ($pricingMode === 'reseller' || $pricingMode === 'special') 
                ? floatval($product['commission_reseller']) 
                : floatval($product['commission_retail'])
        ];
    }

    // Determine discount factor
    $discountFactor = 1.0;
    if ($discount > 0 && $totalValueBeforeDiscount > 0) {
        if ($discount > $totalValueBeforeDiscount) {
            $discount = $totalValueBeforeDiscount; // Cap discount
        }
        $discountFactor = ($totalValueBeforeDiscount - $discount) / $totalValueBeforeDiscount;
    }

    // 2. Insert Order Items & Log Inventory Movement
    $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, raw_cost_applied, price_applied, commission_applied) 
                VALUES (:order_id, :product_id, :quantity, :raw_cost_applied, :price_applied, :commission_applied)";
    $itemStmt = $pdo->prepare($itemSql);

    $ledgerSql = "INSERT INTO inventory_ledger (product_id, movement_id, quantity_change, order_id, notes) 
                  VALUES (:product_id, 2, :qty_change, :order_id, :notes)";
    $ledgerStmt = $pdo->prepare($ledgerSql);

    foreach ($productDetails as $detail) {
        // Distribute discount by multiplying unit price by discount factor
        $priceApplied = round($detail['unit_price'] * $discountFactor, 2);
        
        // Save order items
        $itemStmt->execute([
            'order_id' => $orderId,
            'product_id' => $detail['product_id'],
            'quantity' => $detail['quantity'],
            'raw_cost_applied' => $detail['raw_price'],
            'price_applied' => $priceApplied,
            'commission_applied' => $detail['commission']
        ]);

        // Save inventory ledger (negative change for sales)
        $qtyChange = -($detail['quantity']);
        $ledgerStmt->execute([
            'product_id' => $detail['product_id'],
            'qty_change' => $qtyChange,
            'order_id' => $orderId,
            'notes' => "Sale - Order #$orderId"
        ]);
    }

    // 3. Handle Initial Payment Log
    $grandTotal = max(0, $totalValueBeforeDiscount - $discount);
    if ($paymentStatus === 'Paid') {
        $upd = $pdo->prepare("UPDATE orders SET amount_paid = ? WHERE order_id = ?");
        $upd->execute([$grandTotal, $orderId]);

        $ins = $pdo->prepare("INSERT INTO order_payments (order_id, amount_paid, payment_mode, payment_timestamp) VALUES (?, ?, ?, NOW())");
        $ins->execute([$orderId, $grandTotal, $paymentMode]);
    }

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Order completed successfully',
        'data' => [
            'order_id' => $orderId
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
