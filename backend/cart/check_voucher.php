<?php
header('Content-Type: application/json');
require_once '../config/db.php'; // Verbindung zur DB

$code = $_GET['code'] ?? '';

if (!$code) {
    echo json_encode(['valid' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT prozent FROM vouchers WHERE code = ? AND aktiv = 1");
$stmt->execute([$code]);
$voucher = $stmt->fetch();

if ($voucher) {
    echo json_encode(['valid' => true, 'percent' => $voucher['prozent']]);
} else {
    echo json_encode(['valid' => false]);
}
