<?php

session_start();
require_once("../../config/db.php");

header("Content-Type: application/json");

// Nur eingeloggte Nutzer dürfen fortfahren
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    // Löscht alle Produkte aus dem Warenkorb des Nutzers
    $stmt = $pdo->prepare("DELETE FROM warenkorb WHERE user_id = ?");
    $stmt->execute([$user_id]);

    echo json_encode(["success" => true, "message" => "Warenkorb gelöscht"]);
} catch (PDOException $e) {
    // Fehler beim Löschen
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}
?>
