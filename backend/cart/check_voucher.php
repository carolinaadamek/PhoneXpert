<?php
global $conn;
require_once '../../config/db.php';
header('Content-Type: application/json');

$code = $_GET['code'] ?? '';

if (!$code) {
    echo json_encode(['valid' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT prozent FROM vouchers WHERE code = ? AND aktiv = 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['valid' => true, 'percent' => $row['prozent']]);
} else {
    echo json_encode(['valid' => false]);
}

