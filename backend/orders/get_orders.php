<?php
header('Content-Type: application/json');
require_once '../config/db.php';

// Alle Bestellungen holen, neueste zuerst
$stmt = $db->query("SELECT * FROM orders ORDER BY erstellt_am DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Für jede Bestellung: passende Produkte dazuladen
foreach ($orders as &$order) {
    $orderId = $order['id'];
    $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->execute([$orderId]);
    $order['produkte'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($orders);
