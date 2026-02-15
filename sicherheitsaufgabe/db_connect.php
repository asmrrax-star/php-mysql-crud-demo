<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sicherheitsaufgabe";

// Verbindung herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

?>
