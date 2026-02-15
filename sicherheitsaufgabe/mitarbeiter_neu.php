<?php
// Verbindung zur Datenbank einbinden
require 'db_connect.php';

$errors = [];
$vorname = $nachname = $strasse = $nummer = $plz = $ort = $land = $email = $telefon = $funktion = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eingaben holen und Leerzeichen vorne/hinten entfernen
    $vorname  = trim($_POST['vorname'] ?? '');
    $nachname = trim($_POST['nachname'] ?? '');
    $strasse  = trim($_POST['strasse'] ?? '');
    $nummer   = trim($_POST['nummer'] ?? '');
    $plz      = trim($_POST['plz'] ?? '');
    $ort      = trim($_POST['ort'] ?? '');
    $land     = trim($_POST['land'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $telefon  = trim($_POST['telefon'] ?? '');
    $funktion = trim($_POST['funktion'] ?? '');
    $passwort  = $_POST['passwort']  ?? '';
    $passwort2 = $_POST['passwort2'] ?? '';

    
    // 1. Vorname & Nachname: nur Buchstaben (inkl. Umlaute)
    if ($vorname === '' || !preg_match('/^[A-Za-zÄÖÜäöüß]+$/u', $vorname)) {
        $errors[] = "Vorname darf nur aus Buchstaben bestehen und darf nicht leer sein.";
    }

    if ($nachname === '' || !preg_match('/^[A-Za-zÄÖÜäöüß]+$/u', $nachname)) {
        $errors[] = "Nachname darf nur aus Buchstaben bestehen und darf nicht leer sein.";
    }

    // 2. PLZ: genau 4 Ziffern (0000–9999)
    if ($plz === '' || !preg_match('/^\d{4}$/', $plz)) {
        $errors[] = "PLZ muss genau 4 Ziffern (0000–9999) enthalten.";
    }

    // 3. Telefonnummer: genau 10 Ziffern
    if ($telefon === '' || !preg_match('/^\d{10}$/', $telefon)) {
        $errors[] = "Telefonnummer muss genau 10 Ziffern enthalten.";
    }

    // 4. Strassen-Nr: Zahl zwischen 1 und 100
    if ($nummer === '' || !ctype_digit($nummer)) {
        $errors[] = "Strassennummer muss eine Zahl zwischen 1 und 100 sein.";
    } else {
        $nrInt = (int)$nummer;
        if ($nrInt < 1 || $nrInt > 100) {
            $errors[] = "Strassennummer muss zwischen 1 und 100 liegen.";
        }
    }

    // 5. E-Mail: Pflichtfeld und gültiges Format
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Bitte eine gültige E-Mail-Adresse eingeben.";
    }

    // 6. Passwort: zweimal eingeben und mindestens 8 Zeichen
    if ($passwort === '' || $passwort2 === '') {
        $errors[] = "Passwort muss zweimal eingegeben werden.";
    } elseif ($passwort !== $passwort2) {
        $errors[] = "Die Passwörter stimmen nicht überein.";
    } elseif (strlen($passwort) < 8) {
        $errors[] = "Das Passwort muss mindestens 8 Zeichen lang sein.";
    }

    // Wenn keine Fehler vorhanden sind => Datensatz speichern
    if (empty($errors)) {
        // Passwort verschlüsseln vor dem Speichern
        $passwortHash = password_hash($passwort, PASSWORD_DEFAULT);

        // SQL-Statement zum Einfügen vorbereiten
        $stmt = $conn->prepare("
            INSERT INTO mitarbeiter
            (vorname, nachname, strasse, nummer, plz, ort, land, email, telefon, funktion, passwort)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            die("Fehler beim Prepared Statement: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssissssss",
            $vorname,
            $nachname,
            $strasse,
            $nummer,
            $plz,
            $ort,
            $land,
            $email,
            $telefon,
            $funktion,
            $passwortHash
        );

        if ($stmt->execute()) {
            // Nach erfolgreichem Speichern zurück zur Übersicht
            header("Location: mitarbeiter_liste.php");
            exit;
        } else {
            $errors[] = "Fehler beim Speichern: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Neuen Mitarbeiter erfassen</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Neuen Mitarbeiter erfassen</h1>

    <p><a href="mitarbeiter_liste.php">&laquo; Zurück zur Übersicht</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Formular für neuen Mitarbeiter -->
    <form method="post">
        <label>Vorname:
            <input type="text" name="vorname" value="<?= htmlspecialchars($vorname) ?>">
        </label>

        <label>Nachname:
            <input type="text" name="nachname" value="<?= htmlspecialchars($nachname) ?>">
        </label>

        <label>Strasse:
            <input type="text" name="strasse" value="<?= htmlspecialchars($strasse) ?>">
        </label>

        <label>Nr:
            <input type="text" name="nummer" value="<?= htmlspecialchars($nummer) ?>">
        </label>

        <label>PLZ:
            <input type="text" name="plz" value="<?= htmlspecialchars($plz) ?>">
        </label>

        <label>Ort:
            <input type="text" name="ort" value="<?= htmlspecialchars($ort) ?>">
        </label>

        <label>Land:
            <input type="text" name="land" value="<?= htmlspecialchars($land) ?>">
        </label>

        <label>E-Mail:
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
        </label>

        <label>Telefon:
            <input type="text" name="telefon" value="<?= htmlspecialchars($telefon) ?>">
        </label>

        <label>Funktion:
            <input type="text" name="funktion" value="<?= htmlspecialchars($funktion) ?>">
        </label>

        <label>Passwort:
            <input type="password" name="passwort">
        </label>

        <label>Passwort wiederholen:
            <input type="password" name="passwort2">
        </label>

        <button type="submit">Speichern</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
