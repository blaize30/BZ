<?php
// config/db.php

$host = 'db.fr-roub1.bengt.wasmernet.com';
$db   = 'bloomscentz';
$user = 'user_23e759c2';
$pass = 'pw_uaaykXCZZCrIrK8VbfkGo1GcjXLb577P';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Auto-seed movement_types if empty
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM movement_types");
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $seedData = [
            ['movement_id' => 1, 'movement_name' => 'Restock'],
            ['movement_id' => 2, 'movement_name' => 'Sale'],
            ['movement_id' => 3, 'movement_name' => 'Adjustment'],
            ['movement_id' => 4, 'movement_name' => 'Return']
        ];
        
        $insertStmt = $pdo->prepare("INSERT INTO movement_types (movement_id, movement_name) VALUES (:movement_id, :movement_name)");
        foreach ($seedData as $row) {
            $insertStmt->execute($row);
        }
    }
} catch (\PDOException $e) {
    // Fail silently or handle table not exists
}
