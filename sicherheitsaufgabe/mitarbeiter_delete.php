<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';

// Prüfen ob eine ID übergeben wurde
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Ungültige oder fehlende ID.");
}

$id = (int)$_GET['id'];

// Mitarbeiter anhand der ID löschen 
$stmt = $conn->prepare("DELETE FROM mitarbeiter WHERE id = ?");
if (!$stmt) {
    die("Prepared-Statement-Fehler: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    // Zurück zur Übersicht
    header("Location: mitarbeiter_liste.php");
    exit;
} else {
    $fehler = $stmt->error;
    $stmt->close();
    $conn->close();
    die("Fehler beim Löschen: " . $fehler);
}
