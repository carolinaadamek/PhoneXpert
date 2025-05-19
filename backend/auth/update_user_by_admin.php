<?php

global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Nicht berechtigt"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$vorname = $data['vorname'];
$nachname = $data['nachname'];
$username = $data['username'];
$email = $data['email'];
$typ = $data['typ'];
if (!isset($data['status'])) {
    echo json_encode(["status" => "error", "message" => "Status fehlt!"]);
    exit;
}
$status = $data['status'];
  // fallback für Sicherheit



$stmt = $conn->prepare("UPDATE benutzer SET vorname=?, nachname=?, username=?, email=?, typ=?, status=? WHERE id=?");
$stmt->bind_param("ssssssi", $vorname, $nachname, $username, $email, $typ, $status, $id);


if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Benutzerdaten aktualisiert."]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Fehler beim Speichern: " . $stmt->error
    ]);

}
