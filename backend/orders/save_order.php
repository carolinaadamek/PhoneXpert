<?php
session_start();
global $conn;
header('Content-Type: application/json');
require_once '../../config/db.php';

$userId = $_SESSION['user_id'] ?? null;
// JSON-Daten einlesen
$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['cart']) ||
    !is_array($data['cart']) ||
    empty($data['lieferadresse']) ||
    empty($data['rechnungsadresse'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige oder fehlende Daten.']);
    exit;
}

$cart = $data['cart'];
$lieferadresse = $conn->real_escape_string(trim($data['lieferadresse']));
$rechnungsadresse = $conn->real_escape_string(trim($data['rechnungsadresse']));
$gutscheincode = isset($data['gutscheincode']) ? $conn->real_escape_string($data['gutscheincode']) : null;
$rabatt = 0; // Standardwert

if (!empty($gutscheincode)) {
    $stmt = $conn->prepare("SELECT prozent FROM vouchers WHERE code = ? AND aktiv = 1");
    $stmt->bind_param("s", $gutscheincode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $rabatt = floatval($row['prozent']); // ✅ DB-Wert überschreibt alles
    }
}

// Gesamtsumme berechnen
$gesamt = 0;
foreach ($cart as $item) {
    if (!isset($item['price'], $item['quantity'])) continue;
    $gesamt += floatval($item['price']) * intval($item['quantity']);
}

if ($rabatt > 0 && $rabatt <= 100) {
    $gesamt *= (1 - ($rabatt / 100));
}

// Transaktion starten
$conn->begin_transaction();

try {
    // Bestellung einfügen
    $stmt = $conn->prepare("INSERT INTO orders (user_id, lieferadresse, rechnungsadresse, gutscheincode, rabatt, gesamt, erstellt_am) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssdd", $userId, $lieferadresse, $rechnungsadresse, $gutscheincode, $rabatt, $gesamt);

    $stmt->execute();

    $orderId = $stmt->insert_id;

    // Produkte einfügen
    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, produktname, preis, menge) VALUES (?, ?, ?, ?)");

    foreach ($cart as $item) {
        if (!isset($item['name'], $item['price'], $item['quantity'])) continue;
        $name = $conn->real_escape_string($item['name']);
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $itemStmt->bind_param("isdi", $orderId, $name, $price, $quantity);
        $itemStmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $orderId]);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Fehler beim Speichern der Bestellung.',
        'error' => $e->getMessage()
    ]);
    exit;
}

?>
