<?php
session_start();
require_once("../../config/db.php");

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    $stmt = $pdo->prepare("SELECT * FROM warenkorb WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "items" => $items]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}
?>
