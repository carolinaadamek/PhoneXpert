<?php
$host = "localhost";
$dbname = "phonexpert_webshop";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

?>