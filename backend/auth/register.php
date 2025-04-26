<?php
global $conn;
header('Content-Type: application/json');
include '../../config/db.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$vorname = $_POST['vorname'];
$nachname = $_POST['nachname'];

$check = $conn->prepare("SELECT id FROM benutzer WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Username oder E-Mail existiert bereits."]);
    exit;
}



$hash = password_hash($password, PASSWORD_DEFAULT);
$typ = 'kunde';

$stmt = $conn->prepare("INSERT INTO benutzer (username, email, passwort, typ, vorname, nachname) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $username, $email, $hash, $typ, $vorname, $nachname);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registrierung erfolgreich!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Fehler beim Speichern."]);
}
?>
