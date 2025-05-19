<?php
global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');


// 🔒 Zugriff prüfen
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$userId = $_SESSION['user_id'];

// 🔍 Nur Bestellungen dieses Benutzers abrufen
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY erstellt_am DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($order = $result->fetch_assoc()) {
    // 📦 Produkte zur Bestellung laden
    $posStmt = $conn->prepare("SELECT produktname, preis, menge FROM order_items WHERE order_id = ?");
    $posStmt->bind_param("i", $order['id']);
    $posStmt->execute();
    $posResult = $posStmt->get_result();

    $items = [];
    while ($item = $posResult->fetch_assoc()) {
        $items[] = $item;
    }

    $order['items'] = $items;
    $orders[] = $order;
}

// ✅ Ausgabe
echo json_encode($orders);
exit;


