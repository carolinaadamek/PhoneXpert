<?php
global $conn;
require_once '../../config/db.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT id, name, preis, beschreibung, image_path FROM produkt ORDER BY id DESC");
$produkte = [];

while ($row = $result->fetch_assoc()) {
    $produkte[] = $row;
}

echo json_encode($produkte);
