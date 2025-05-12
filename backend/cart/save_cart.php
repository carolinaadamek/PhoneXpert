<?php
session_start();
require_once("../../config/db.php");

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["cart"])) {
    echo json_encode(["success" => false, "message" => "Kein Warenkorb übergeben"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$cart = $data["cart"];

try {
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
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}
?>
