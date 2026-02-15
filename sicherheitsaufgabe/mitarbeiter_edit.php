<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';

$errors = [];
// Standardwerte für das Formular
$id = $vorname = $nachname = $strasse = $nummer = $plz = $ort = $land = $email = $telefon = $funktion = "";
$passwortHinweis = "Passwort leer lassen, um es nicht zu ändern.";

// Formular absenden (UPDATE per POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'] ?? '';
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
    $passwort = $_POST['passwort']  ?? '';
    $passwort2 = $_POST['passwort2'] ?? '';

    // --- ID überprüfen ---
    if ($id === '' || !ctype_digit($id)) {
        $errors[] = "Ungültige ID.";
    }

    // --- 1. Vorname und Nachname: nur Buchstaben ---
    if ($vorname === '' || !preg_match('/^[A-Za-zÄÖÜäöüß]+$/u', $vorname)) {
        $errors[] = "Vorname darf nur aus Buchstaben bestehen und darf nicht leer sein.";
    }

    if ($nachname === '' || !preg_match('/^[A-Za-zÄÖÜäöüß]+$/u', $nachname)) {
        $errors[] = "Nachname darf nur aus Buchstaben bestehen und darf nicht leer sein.";
    }

    // --- 2. PLZ: genau 4 Ziffern (0000–9999) ---
    if ($plz === '' || !preg_match('/^\d{4}$/', $plz)) {
        $errors[] = "PLZ muss genau 4 Ziffern (0000–9999) enthalten.";
    }

    // --- 3. Telefonnummer: genau 10 Ziffern ---
    if ($telefon === '' || !preg_match('/^\d{10}$/', $telefon)) {
        $errors[] = "Telefonnummer muss genau 10 Ziffern enthalten.";
    }

    // --- 4. Strassen-Nr: Zahl zwischen 1 und 100 ---
    if ($nummer === '' || !ctype_digit($nummer)) {
        $errors[] = "Strassennummer muss eine Zahl zwischen 1 und 100 sein.";
    } else {
        $nrInt = (int)$nummer;
        if ($nrInt < 1 || $nrInt > 100) {
            $errors[] = "Strassennummer muss zwischen 1 und 100 liegen.";
        }
    }

    // --- 5. E-Mail: Pflichtfeld + gültiges Format ---
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Bitte eine gültige E-Mail-Adresse eingeben.";
    }

    // --- 6. Passwort nur prüfen wenn etwas eingegeben wurde ---
    $neuesPasswortHash = null;
    if ($passwort !== '' || $passwort2 !== '') {
        if ($passwort === '' || $passwort2 === '') {
            $errors[] = "Neues Passwort muss zweimal eingegeben werden.";
        } elseif ($passwort !== $passwort2) {
            $errors[] = "Die neuen Passwörter stimmen nicht überein.";
        } elseif (strlen($passwort) < 8) {
            $errors[] = "Das neue Passwort muss mindestens 8 Zeichen lang sein.";
        } else {
            $neuesPasswortHash = password_hash($passwort, PASSWORD_DEFAULT);
        }
    }

    if (empty($errors)) {
        if ($neuesPasswortHash === null) {
            // Update ohne Passwortänderung
            $stmt = $conn->prepare("
                UPDATE mitarbeiter
                SET vorname = ?, nachname = ?, strasse = ?, nummer = ?, plz = ?, ort = ?, land = ?, email = ?, telefon = ?, funktion = ?
                WHERE id = ?
            ");
            if (!$stmt) {
                die("Prepared-Statement-Fehler: " . $conn->error);
            }

            $stmt->bind_param(
                "ssssisssssi",
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
                $id
            );
        } else {
            // Update mit neuem Passwort
            $stmt = $conn->prepare("
                UPDATE mitarbeiter
                SET vorname = ?, nachname = ?, strasse = ?, nummer = ?, plz = ?, ort = ?, land = ?, email = ?, telefon = ?, funktion = ?, passwort = ?
                WHERE id = ?
            ");
            if (!$stmt) {
                die("Prepared-Statement-Fehler: " . $conn->error);
            }

            $stmt->bind_param(
                "ssssisssssssi",
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
                $neuesPasswortHash,
                $id
            );
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: mitarbeiter_liste.php");
            exit;
        } else {
            $errors[] = "Fehler beim Speichern: " . $stmt->error;
            $stmt->close();
        }
    }

// Seite per GET aufrufen (Daten werden geladen)
} else {
    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        die("Ungültige oder fehlende ID.");
    }

    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM mitarbeiter WHERE id = ?");
    if (!$stmt) {
        die("Prepared-Statement-Fehler: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $mitarbeiter = $result->fetch_assoc();
    $stmt->close();

    if (!$mitarbeiter) {
        die("Kein Mitarbeiter mit dieser ID gefunden.");
    }

    // Werte ins Formular übernehmen
    $vorname  = $mitarbeiter['vorname'];
    $nachname = $mitarbeiter['nachname'];
    $strasse  = $mitarbeiter['strasse'];
    $nummer   = $mitarbeiter['nummer'];
    $plz      = $mitarbeiter['plz'];
    $ort      = $mitarbeiter['ort'];
    $land     = $mitarbeiter['land'];
    $email    = $mitarbeiter['email'];
    $telefon  = $mitarbeiter['telefon'];
    $funktion = $mitarbeiter['funktion'];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mitarbeiter bearbeiten</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Mitarbeiter bearbeiten</h1>

    <p><a href="mitarbeiter_liste.php">&laquo; Zurück zur Übersicht</a></p>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <!-- ID mitgeben zum Updaten -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <label>Vorname:
            <input type="text" name="vorname" value="<?= htmlspecialchars($vorname) ?>">
        </label><br>

        <label>Nachname:
            <input type="text" name="nachname" value="<?= htmlspecialchars($nachname) ?>">
        </label><br>

        <label>Strasse:
            <input type="text" name="strasse" value="<?= htmlspecialchars($strasse) ?>">
        </label><br>

        <label>Nr:
            <input type="text" name="nummer" value="<?= htmlspecialchars($nummer) ?>">
        </label><br>

        <label>PLZ:
            <input type="text" name="plz" value="<?= htmlspecialchars($plz) ?>">
        </label><br>

        <label>Ort:
            <input type="text" name="ort" value="<?= htmlspecialchars($ort) ?>">
        </label><br>

        <label>Land:
            <input type="text" name="land" value="<?= htmlspecialchars($land) ?>">
        </label><br>

        <label>E-Mail:
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
        </label><br>

        <label>Telefon:
            <input type="text" name="telefon" value="<?= htmlspecialchars($telefon) ?>">
        </label><br>

        <label>Funktion:
            <input type="text" name="funktion" value="<?= htmlspecialchars($funktion) ?>">
        </label><br>

        <p><strong><?= htmlspecialchars($passwortHinweis) ?></strong></p>

        <label>Neues Passwort (optional):
            <input type="password" name="passwort">
        </label><br>

        <label>Neues Passwort wiederholen:
            <input type="password" name="passwort2">
        </label><br><br>

        <button type="submit">Änderungen speichern</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
