<?php
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

global $conn;
// Nur Admin darf Produkte bearbeiten
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Nicht berechtigt"]);
    exit;
}
// JSON-Daten auslese
$data = json_decode(file_get_contents("php://input"), true);
// Grundlegende Felder prüfen
if (!isset($data['id'], $data['name'], $data['preis'], $data['image_path'])) {
    echo json_encode(["status" => "error", "message" => "Ungültige oder fehlende Daten"]);
    exit;
}

// Werte vorbereiten
$id = intval($data['id']);
$name = $conn->real_escape_string($data['name']);
$preis = floatval($data['preis']);
$image_path = $conn->real_escape_string($data['image_path']);
$beschreibung = isset($data['beschreibung']) ? $conn->real_escape_string($data['beschreibung']) : null;

// Produkt aktualisieren
$sql = "UPDATE produkt SET name = ?, preis = ?, image_path = ?, beschreibung = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdssi", $name, $preis, $image_path, $beschreibung, $id);

// Rückmeldung senden
if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update fehlgeschlagen: " . $stmt->error]);
}
