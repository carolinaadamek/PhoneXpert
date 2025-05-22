<?php
// Datenbankverbindung und Session starten
global $conn;
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// Zugriff nur für eingeloggte Benutzer
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Nicht eingeloggt"]);
    exit;
}

// Benutzer-ID aus der Session holen
$userId = $_SESSION['user_id'];

// Benutzerinformationen aus der Datenbank abrufen (für Profilanzeige o. Ä.)
$stmt = $conn->prepare("SELECT vorname, nachname, username, email FROM benutzer WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Erfolgreiche Abfrage: Benutzer gefunden → Daten zurückgeben
if ($result && $result->num_rows === 1) {
    echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
} else {
    // Benutzer-ID existiert nicht oder Fehler bei der Abfrage
    echo json_encode(["status" => "error", "message" => "Benutzer nicht gefunden"]);
}
