<?php
global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

// Nur Admin darf zugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Kein Zugriff"]);
    exit;
}

$sql = "SELECT id, vorname, nachname, username, email, typ, status FROM benutzer ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

echo json_encode($users);
