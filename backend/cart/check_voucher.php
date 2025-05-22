<?php
global $conn;
require_once '../../config/db.php';
header('Content-Type: application/json');

// Gutscheincode aus der URL abfragen 
$code = $_GET['code'] ?? '';

// Wenn kein Code übergeben wurde → direkt abbrechen
if (!$code) {
    echo json_encode(['valid' => false]);
    exit;
}

// SQL-Abfrage: Gültigen Gutschein (aktiv = 1) mit entsprechendem Code suchen
$stmt = $conn->prepare("SELECT prozent FROM vouchers WHERE code = ? AND aktiv = 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

// Wenn Gutschein gefunden → gültig + Prozentwert zurückgeben
if ($row = $result->fetch_assoc()) {
    echo json_encode(['valid' => true, 'percent' => $row['prozent']]);
} else {
    // Kein gültiger Gutschein gefunden
    echo json_encode(['valid' => false]);
}
