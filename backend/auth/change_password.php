<?php
global $conn;
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Nicht eingeloggt."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$oldPw = $data['old_password'];
$newPw = $data['new_password'];
$userId = $_SESSION['user_id'];

// Aktuelles Passwort holen
$stmt = $conn->prepare("SELECT passwort FROM benutzer WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    echo json_encode(["status" => "error", "message" => "Benutzer nicht gefunden."]);
    exit;
}

$row = $result->fetch_assoc();
if (!password_verify($oldPw, $row['passwort'])) {
    echo json_encode(["status" => "error", "message" => "Aktuelles Passwort ist falsch."]);
    exit;
}

// Neues Passwort speichern
$newHash = password_hash($newPw, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE benutzer SET passwort = ? WHERE id = ?");
$update->bind_param("si", $newHash, $userId);
$success = $update->execute();

if ($success) {
    echo json_encode(["status" => "success", "message" => "Passwort erfolgreich geändert."]);
} else {
    echo json_encode(["status" => "error", "message" => "Fehler beim Speichern."]);
}
