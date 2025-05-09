<?php
global $conn;
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Nicht eingeloggt"]);
    exit;
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$vorname = $data['vorname'];
$nachname = $data['nachname'];
$username = $data['username'];
$email = $data['email'];

$stmt = $conn->prepare("UPDATE benutzer SET vorname=?, nachname=?, username=?, email=? WHERE id=?");
$stmt->bind_param("ssssi", $vorname, $nachname, $username, $email, $userId);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Profil aktualisiert"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update fehlgeschlagen"]);
}
