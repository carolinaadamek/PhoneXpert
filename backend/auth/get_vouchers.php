<?php
global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

// Nur Admin darf Gutscheine einsehen
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Nicht berechtigt."]);
    exit;
}

// Gutscheine abrufen
$result = $conn->query("SELECT code, prozent, gueltig_bis, aktiv, erstellt_am FROM vouchers ORDER BY erstellt_am DESC");

$vouchers = [];
while ($row = $result->fetch_assoc()) {
    $vouchers[] = $row;
}

// JSON-Ausgabe
echo json_encode([
    "success" => true,
    "vouchers" => $vouchers
]);
exit;
