<?php
global $conn;
session_start();
header('Content-Type: application/json');
include '../../config/db.php';

// Empfange Login-Daten (Benutzername oder E-Mail, Passwort) vom Frontend via POST
$username = $_POST['username'];
$password = $_POST['password'];

// Benutzer aus der Datenbank abrufen, der entweder zur E-Mail oder zum Benutzernamen passt
$stmt = $conn->prepare("SELECT id, username, email, passwort, typ, vorname, status FROM benutzer WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// Prüfe, ob genau ein Benutzer gefunden wurde
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Wenn Benutzer ein Kunde ist und als inaktiv markiert ist, Login verweigern
    if ($user['typ'] === 'kunde' && isset($user['status']) && $user['status'] === 'inaktiv') {
        echo json_encode(["status" => "error", "message" => "Dein Konto ist deaktiviert."]);
        exit;
    }

    // Passwortprüfung mit gehashter Version in der Datenbank
    if (password_verify($password, $user['passwort'])) {

        // Login erfolgreich: Session-Variablen setzen
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['typ'] = $user['typ'];
        $_SESSION['vorname'] = $user['vorname'];

        echo json_encode(["status" => "success", "message" => "Willkommen, {$user['vorname']}!"]);
    } else {
        // Passwort falsch
        echo json_encode(["status" => "error", "message" => "Falsches Passwort."]);
    }
} else {
    // Kein Benutzer gefunden
    echo json_encode(["status" => "error", "message" => "Benutzer nicht gefunden."]);
}

