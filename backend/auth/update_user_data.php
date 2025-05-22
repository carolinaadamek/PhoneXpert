<?php
global $conn;
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// Nur eingeloggte Benutzer dürfen ihr Profil bearbeiten
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Nicht eingeloggt"]);
    exit;
}

// Benutzer-ID aus der Session lesen
$userId = $_SESSION['user_id'];

// Neue Profildaten vom Client empfangen (JSON aus PUT/POST)
$data = json_decode(file_get_contents("php://input"), true);

// Neue Werte extrahieren
$vorname = $data['vorname'];
$nachname = $data['nachname'];
$username = $data['username'];
$email = $data['email'];

// SQL-Befehl vorbereiten, um Benutzerdaten zu aktualisieren
$stmt = $conn->prepare("UPDATE benutzer SET vorname=?, nachname=?, username=?, email=? WHERE id=?");
$stmt->bind_param("ssssi", $vorname, $nachname, $username, $email, $userId);

// Ergebnis zurückgeben
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Profil aktualisiert"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update fehlgeschlagen"]);
}
