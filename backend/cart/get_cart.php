<?php
session_start();
require_once("../../config/db.php");

header("Content-Type: application/json");

// Nur für eingeloggte Nutzer
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    // Alle Warenkorb-Einträge des Nutzers holen
    $stmt = $pdo->prepare("SELECT * FROM warenkorb WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "items" => $items]);
} catch (PDOException $e) {
    // Fehlerfall
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}
?>
