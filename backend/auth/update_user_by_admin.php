<?php


global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

// Nur eingeloggte Admins dürfen auf diese Funktion zugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Nicht berechtigt"]);
    exit;
}

// Empfang der JSON-Daten vom Frontend (PUT/POST mit JSON body)
$data = json_decode(file_get_contents("php://input"), true);

// Extrahieren der übergebenen Benutzerdaten
$id = $data['id'];
$vorname = $data['vorname'];
$nachname = $data['nachname'];
$username = $data['username'];
$email = $data['email'];
$typ = $data['typ'];

// Prüfung, ob ein Status übergeben wurde – ist Pflichtfeld
if (!isset($data['status'])) {
    echo json_encode(["status" => "error", "message" => "Status fehlt!"]);
    exit;
}
$status = $data['status'];  // Fallback zur Sicherheit

// SQL-Statement vorbereiten: Benutzer mit übergebenen Werten aktualisieren
$stmt = $conn->prepare("UPDATE benutzer SET vorname=?, nachname=?, username=?, email=?, typ=?, status=? WHERE id=?");
$stmt->bind_param("ssssssi", $vorname, $nachname, $username, $email, $typ, $status, $id);

// Rückmeldung an das Frontend, ob das Update erfolgreich war
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Benutzerdaten aktualisiert."]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Fehler beim Speichern: " . $stmt->error
    ]);
}
