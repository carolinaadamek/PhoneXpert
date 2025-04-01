<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "loggedIn" => true,
        "username" => $_SESSION['username'],
        "vorname" => $_SESSION['vorname']
    ]);
} else {
    echo json_encode(["loggedIn" => false]);
}

