<?php
global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

// Zugriffsbeschränkung: Nur Admins dürfen alle Benutzer sehen
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Kein Zugriff"]);
    exit;
}

// SQL-Abfrage: Alle Benutzer aus der Datenbank abrufen
$sql = "SELECT id, vorname, nachname, username, email, typ, status FROM benutzer ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

// Ergebnis in ein Array umwandeln
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

// Rückgabe aller Benutzerdaten im JSON-Format
echo json_encode($users);
