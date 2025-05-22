<?php
$pdo = new PDO("mysql:host=localhost;dbname=phonexpert_webshop;charset=utf8", "root", "");
require '../../lib/fpdf186/fpdf.php'; // Passe Pfad an, falls nötig

if (!isset($_GET['id'])) {
  die("Keine Bestell-ID angegeben");
}

$orderId = intval($_GET['id']);

//  Bestelldaten aus DB holen (inkl. Produkte, User, Adressen usw.)
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
  die("Bestellung nicht gefunden.");
}

$fpdf = new FPDF();
$fpdf->AddPage();
$fpdf->SetFont('Arial', '', 12);

//  Logo oben rechts
$fpdf->Image('../../frontend/img/logo.png', 150, 10, 40);

//  Firmenanschrift
$fpdf->Cell(0, 10, 'PhoneXpert GmbH', 0, 1);
$fpdf->Cell(0, 8, 'Hofstädterstraße 6, 1200 Wien', 0, 1);
$fpdf->Cell(0, 8, 'kontakt@phonexpert.at', 0, 1);
$fpdf->Ln(10);

//  Rechnungstitel
$fpdf->SetFont('Arial', 'B', 16);
$fpdf->Cell(0, 10, 'Rechnung', 0, 1, 'C');
$fpdf->Ln(5);

//  Rechnungsdaten
$fpdf->SetFont('Arial', '', 12);
$fpdf->Cell(0, 8, 'Rechnungsdatum: ' . date('d.m.Y', strtotime($order['erstellt_am'])), 0, 1);
$fpdf->Cell(0, 8, 'Rechnungsadresse: ' . $order['rechnungsadresse'], 0, 1);
$fpdf->Cell(0, 8, 'Gutscheincode: ' . ($order['gutscheincode'] ?: '-'), 0, 1);
$fpdf->Cell(0, 8, 'Rabatt: ' . ($order['rabatt'] ?? 0) . '%', 0, 1);
$fpdf->Ln(5);

//  Produkte laden
$stmt2 = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt2->execute([$orderId]);
$items = $stmt2->fetchAll();

//  Produkttabelle
$fpdf->SetFont('Arial', 'B', 12);
$fpdf->Cell(80, 8, 'Produkt', 1);
$fpdf->Cell(30, 8, 'Menge', 1);
$fpdf->Cell(40, 8, 'Einzelpreis', 1);
$fpdf->Cell(40, 8, 'Gesamt', 1);
$fpdf->Ln();

$fpdf->SetFont('Arial', '', 12);
foreach ($items as $item) {
  $gesamt = $item['menge'] * $item['preis'];
  $fpdf->Cell(80, 8, $item['produktname'], 1);
  $fpdf->Cell(30, 8, $item['menge'], 1);
  $fpdf->Cell(40, 8, number_format($item['preis'], 2) . ' €', 1);
  $fpdf->Cell(40, 8, number_format($gesamt, 2) . ' €', 1);
  $fpdf->Ln();
}

$fpdf->Ln(5);
$fpdf->SetFont('Arial', 'B', 12);
$fpdf->Cell(0, 10, 'Gesamtsumme: ' . number_format($order['gesamt'], 2) . ' €', 0, 1);

$fpdf->Output('I', 'Rechnung_' . $orderId . '.pdf');
