<?php
global $conn;
require_once '../../config/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Produkte abrufen 
$sql = "SELECT id, name, beschreibung, preis, image_path FROM produkt ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Fehlerbehandlung bei der Abfrage
if (!$result) {
    echo json_encode(["error" => "DB-Fehler: " . mysqli_error($conn)]);
    exit;
}

// Ergebnisse sammeln
$produkte = [];
while ($row = mysqli_fetch_assoc($result)) {
    $produkte[] = $row;
}


echo json_encode($produkte);
