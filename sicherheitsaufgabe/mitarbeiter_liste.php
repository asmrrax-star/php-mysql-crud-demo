<?php
// Verbindung zur Datenbank einbinden
require 'db_connect.php';

// Alle Mitarbeiter holen
$sql = "SELECT * FROM mitarbeiter";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mitarbeiter Übersicht</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Mitarbeiter Übersicht</h1>

    <!-- Link für neuen Eintrag -->
    <p><a href="mitarbeiter_neu.php">Neuen Mitarbeiter erfassen</a></p>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Strasse</th>
                <th>Nr</th>
                <th>PLZ</th>
                <th>Ort</th>
                <th>Land</th>
                <th>E-Mail</th>
                <th>Telefon</th>
                <th>Funktion</th>
                <th>Aktionen</th>
            </tr>

            <!-- Alle Datensätze Zeile für Zeile anzeigen -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['vorname']) ?></td>
                    <td><?= htmlspecialchars($row['nachname']) ?></td>
                    <td><?= htmlspecialchars($row['strasse']) ?></td>
                    <td><?= htmlspecialchars($row['nummer']) ?></td>
                    <td><?= htmlspecialchars($row['plz']) ?></td>
                    <td><?= htmlspecialchars($row['ort']) ?></td>
                    <td><?= htmlspecialchars($row['land']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefon']) ?></td>
                    <td><?= htmlspecialchars($row['funktion']) ?></td>
                    <td>
                        <a href="mitarbeiter_edit.php?id=<?= $row['id'] ?>">Bearbeiten</a> |
                        <a href="mitarbeiter_delete.php?id=<?= $row['id'] ?>"
                           onclick="return confirm('Wirklich löschen?');">Löschen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php else: ?>
        <p>Keine Mitarbeiter gefunden.</p>
    <?php endif; ?>

</body>
</html>

<?php
// Verbindung wieder schließen
$conn->close();
?>
