<?php
// backend/orders/save_order.php

header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart'], $data['lieferadresse'], $data['rechnungsadresse'], $data['gesamt'], $data['gutscheincode'], $data['rabatt'])) {
    echo json_encode(['success' => false, 'message' => 'Ungültige Daten']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (lieferadresse, rechnungsadresse, gutscheincode, rabatt, gesamt) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['lieferadresse'],
        $data['rechnungsadresse'],
        $data['gutscheincode'],
        $data['rabatt'],
        $data['gesamt']
    ]);

    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, produktname, preis, menge) VALUES (?, ?, ?, ?)");
    foreach ($data['cart'] as $item) {
        $itemStmt->execute([$orderId, $item['name'], $item['price'], $item['quantity']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
