<?php
// Fehlerausgabe aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// JSON Header setzen
header('Content-Type: application/json');

// DB-Verbindung laden
require_once __DIR__ . '/../../config/db.php';

// Verbindung prüfen
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['error' => 'Keine gültige Datenbankverbindung']);
    exit;
}

try {
    $orders = [];

    // Bestellungen + Benutzerinfo abrufen
    $sql = "
        SELECT 
            o.*, 
            b.username, 
            b.vorname, 
            b.nachname
        FROM orders o
        LEFT JOIN benutzer b ON o.user_id = b.id
        ORDER BY o.erstellt_am DESC
    ";

    $result = $conn->query($sql);

    while ($order = $result->fetch_assoc()) {
        $orderId = $order['id'];

        // Bestellpositionen abrufen
        $itemQuery = $conn->prepare("SELECT produktname, preis, menge FROM order_items WHERE order_id = ?");
        $itemQuery->bind_param("i", $orderId);
        $itemQuery->execute();
        $itemResult = $itemQuery->get_result();

        $order['items'] = [];
        while ($item = $itemResult->fetch_assoc()) {
            $order['items'][] = $item;
        }

        $orders[] = $order;
    }

    echo json_encode($orders);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Fehler beim Abrufen der Bestellungen',
        'details' => $e->getMessage()
    ]);
    exit;
}
