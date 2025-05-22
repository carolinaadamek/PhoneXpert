<?php
session_start();
require_once("../../config/db.php");

header("Content-Type: application/json");

// Zugriff nur für eingeloggte Nutzer
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

// JSON-Daten (Warenkorb) vom Frontend holen
$data = json_decode(file_get_contents("php://input"), true);

// Prüfen, ob ein Warenkorb übergeben wurde
if (!isset($data["cart"])) {
    echo json_encode(["success" => false, "message" => "Kein Warenkorb übergeben"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$cart = $data["cart"];

try {
    // Einfügen jedes Produkts in die DB
    $stmt = $pdo->prepare("INSERT INTO warenkorb (user_id, produkt_id, menge, produkt_name, preis, hinzugefuegt_am)
                           VALUES (?, ?, ?, ?, ?, NOW())");

    foreach ($cart as $item) {
        $stmt->execute([
            $user_id,
            $item["id"],
            $item["quantity"],
            $item["name"],
            $item["price"]
        ]);
    }

    echo json_encode(["success" => true, "message" => "Warenkorb gespeichert"]);
} catch (PDOException $e) {
    // Fehler beim Speichern
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}
?>
