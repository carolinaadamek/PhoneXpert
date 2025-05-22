<?php

global $conn;
require_once '../../config/db.php';

header('Content-Type: application/json');

// Suchbegriff aus URL holen
$search = isset($_GET['query']) ? trim($_GET['query']) : '';

// Wenn leer, leere Liste zurückgeben
if ($search === '') {
    echo json_encode([]);
    exit;
}

// Produkte nach Name oder Beschreibung filtern
$sql = "SELECT id, name, beschreibung, preis, image_path FROM produkt WHERE name LIKE ? OR beschreibung LIKE ? ORDER BY id DESC";
$stmt = mysqli_prepare($conn, $sql);

$searchTerm = "%{$search}%";
mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
mysqli_stmt_execute($stmt);

// Ergebnisse sammeln
$result = mysqli_stmt_get_result($stmt);
$produkte = [];
while ($row = mysqli_fetch_assoc($result)) {
    $produkte[] = $row;
}

echo json_encode($produkte);
