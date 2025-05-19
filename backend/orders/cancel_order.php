<?php
global $conn;
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['typ'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Kein Zugriff"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);

$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
exit;
