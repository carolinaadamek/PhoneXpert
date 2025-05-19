<?php
global $conn;
session_start();
header('Content-Type: application/json');
include '../../config/db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, username, email, passwort, typ, vorname, status FROM benutzer WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($user['typ'] === 'kunde' && isset($user['status']) && $user['status'] === 'inaktiv') {
        echo json_encode(["status" => "error", "message" => "Dein Konto ist deaktiviert."]);
        exit;
    }

    if (password_verify($password, $user['passwort'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['typ'] = $user['typ'];
        $_SESSION['vorname'] = $user['vorname'];

        echo json_encode(["status" => "success", "message" => "Willkommen, {$user['vorname']}!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Falsches Passwort."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Benutzer nicht gefunden."]);
}
