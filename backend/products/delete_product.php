<?php
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

global $conn;

// Nur Admin darf löschen
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Nicht berechtigt"]);
    exit;
}
// Produkt-ID aus JSON holen
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM produkt WHERE id = ?");
$stmt->bind_param("i", $id);

// Rückmeldung je nach Erfolg
if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Löschen fehlgeschlagen."]);
}