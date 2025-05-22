<?php
// Verbindung zur Datenbank einbinden
global $conn;
header('Content-Type: application/json');
include '../../config/db.php';


$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$vorname = $_POST['vorname'];
$nachname = $_POST['nachname'];

// Überprüfen, ob Username oder E-Mail bereits existieren
$check = $conn->prepare("SELECT id FROM benutzer WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Wenn Benutzername oder E-Mail bereits vergeben sind, Fehler zurückgeben
    echo json_encode(["status" => "error", "message" => "Username oder E-Mail existiert bereits."]);
    exit;
}

// Passwort verschlüsseln (hashen) für sichere Speicherung
$hash = password_hash($password, PASSWORD_DEFAULT);
$typ = 'kunde'; // Standardmäßig wird ein neuer Benutzer als "kunde" angelegt

// Benutzer in der Datenbank speichern
$stmt = $conn->prepare("INSERT INTO benutzer (username, email, passwort, typ, vorname, nachname) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $username, $email, $hash, $typ, $vorname, $nachname);

// Rückmeldung an das Frontend, ob die Registrierung erfolgreich war
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registrierung erfolgreich!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Fehler beim Speichern."]);
}
?>