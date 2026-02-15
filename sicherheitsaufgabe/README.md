# Mitarbeiterverwaltung – Sicherheitsaufgabe (PHP / MySQL)

Dieses Projekt entstand im Rahmen einer Modulabschlussprüfung während meiner Ausbildung zum Informatiker EFZ (Applikationsentwicklung).
Ziel der Aufgabe war die Umsetzung einer einfachen Webanwendung zur Verwaltung von Mitarbeitern unter besonderer Berücksichtigung grundlegender Sicherheitsaspekte.

## Projektbeschreibung

Die Anwendung ermöglicht die Verwaltung von Mitarbeitern über eine klassische CRUD-Architektur und demonstriert den Einsatz von serverseitiger Validierung, sicherer Datenbankanbindung sowie geschützter Passwortspeicherung.

## Funktionen

* Mitarbeiter anzeigen (Übersicht)
* Neue Mitarbeiter erfassen
* Mitarbeiterdaten bearbeiten
* Mitarbeiter löschen
* Eingaben validieren
* Passwörter sicher speichern (Hashing)

## Technische Umsetzung

Die Anwendung basiert auf einer klassischen LAMP-ähnlichen Struktur (lokal mit XAMPP):

* **PHP** – Serverseitige Logik
* **MySQL** – Relationale Datenbank
* **Apache (XAMPP)** – Lokale Entwicklungsumgebung
* **HTML / CSS** – Benutzeroberfläche

## Sicherheitsaspekte

Im Fokus der Aufgabe standen grundlegende Web-Sicherheitsmechanismen:

* Verwendung von **Prepared Statements** zum Schutz vor SQL-Injection
* Serverseitige Validierung der Eingaben
* Sichere Passwortspeicherung mit `password_hash()`
* Trennung von Datenbankverbindung und Anwendungslogik (`db_connect.php`)

## Datenbank

Die benötigte Datenbankstruktur ist in der Datei
`sicherheitsaufgabe.sql` enthalten und kann direkt in MySQL importiert werden.

## Lokale Ausführung

1. Projekt in das `htdocs`-Verzeichnis von XAMPP kopieren
2. Apache und MySQL starten
3. Datenbank über `sicherheitsaufgabe.sql` importieren
4. Anwendung im Browser aufrufen:

   ```
   http://localhost/sicherheitsaufgabe
   ```

## Zweck des Projekts

Dieses Projekt dient ausschließlich zu Lern- und Demonstrationszwecken und zeigt die praktische Umsetzung grundlegender Konzepte der sicheren Webentwicklung mit PHP und MySQL.
