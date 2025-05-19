<?php
global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

// Nur Admin darf Gutscheine erstellen
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Nicht berechtigt."]);
    exit;
}

// Eingabedaten lesen
$data = json_decode(file_get_contents("php://input"), true);

$percent = intval($data['percent']);
$expires = !empty($data['expires']) ? $data['expires'] : null;

if ($percent < 1 || $percent > 100) {
    echo json_encode(["success" => false, "message" => "Ungültiger Rabattwert."]);
    exit;
}

// Zufälliger Gutscheincode (z. B. 8-stellig hex)
$code = strtoupper(bin2hex(random_bytes(4)));

$stmt = $conn->prepare("INSERT INTO vouchers (code, prozent, gueltig_bis, aktiv) VALUES (?, ?, ?, 1)");
$stmt->bind_param("sis", $code, $percent, $expires);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "code" => $code]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Fehler beim Speichern.",
        "error" => $stmt->error
    ]);
}
exit;
