<?php
require_once '../config/db.php'; // Passe ggf. den Pfad an

header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM orders ORDER BY erstellt_am DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as &$order) {
        $orderId = $order['id'];
        $stmtItems = $db->prepare("SELECT produktname, preis, menge FROM order_items WHERE order_id = ?");
        $stmtItems->execute([$orderId]);
        $order['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($orders);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Fehler beim Abrufen der Bestellungen',
        'details' => $e->getMessage()
    ]);
    exit;
}
