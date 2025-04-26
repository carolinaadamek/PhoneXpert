<?php
global $conn;
session_start();
header('Content-Type: application/json');
require_once '../../config/db.php'; // Verbindet mit $conn
// Admin-Prüfung
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Zugriff verweigert."]);
    exit;
}

// Validierung
if (!isset($_POST['name'], $_POST['description'], $_POST['price']) || !is_numeric($_POST['price'])) {
    echo json_encode(["status" => "error", "message" => "Ungültige Eingabedaten."]);
    exit;
}

// Bild speichern
$uploadDir = '../../frontend/img/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$imageName = time() . "_" . basename($_FILES['image']['name']);
$imagePath = $uploadDir . $imageName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
    echo json_encode(["status" => "error", "message" => "Bild konnte nicht hochgeladen werden."]);
    exit;
}

// Daten speichern mit mysqli
$stmt = mysqli_prepare($conn, "INSERT INTO produkt (name, beschreibung, preis, image_path) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssds", $_POST['name'], $_POST['description'], $_POST['price'], $imageName);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => "Produkt erfolgreich gespeichert."]);
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Fehler beim Speichern."]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

