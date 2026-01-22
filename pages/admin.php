<?php
session_start();
//Header einbinden
include '../functions/header.php';
//Überprüfen ob Admin eingeloggt ist
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "10032008", "bibliothek_mtsp");
$conn->set_charset("utf8");
if ($conn->connect_error)
    die("DB Fehler: " . $conn->connect_error);
//Hilfsfunktionen
function es($str)
{
    global $conn;
    return $conn->real_escape_string($str);
}
function redirect()
{
    header("Location: admin.php");
    exit();
}
//Buch hinzufügen oder aktualisieren
if (isset($_POST['add']) || isset($_POST['update'])) {
    $isbn = es($_POST['isbn']);
    $titel = es($_POST['titel']);
    $beschreibung = es($_POST['beschreibung']);
    $verlag = es($_POST['verlag']);
    $preis = floatval($_POST['preis']);
    $kategorie = es($_POST['kategorie']);
    //Buch hinzufügen
    if (isset($_POST['add'])) {
        //SQL Befehl zum Einfügen eines neuen Buches
        $conn->query("INSERT INTO buecher (isbn, titel, beschreibung, verlag, anschaffungspreis, kategorie)
                      VALUES ('$isbn', '$titel', '$beschreibung', '$verlag', '$preis', '$kategorie')");
    } else {
        //SQL Befehl zum Aktualisieren eines Buches
        $buch_id = intval($_POST['buch_id']);
        $conn->query("UPDATE buecher SET titel='$titel', beschreibung='$beschreibung', verlag='$verlag', anschaffungspreis='$preis', kategorie='$kategorie'
                      WHERE buch_id='$buch_id'");
    }
    //Seit neu laden
    redirect();
}
//Buch löschen
if (isset($_GET['delete'])) {
    $buch_id = intval($_GET['delete']);
    $conn->query("DELETE FROM ausleihen WHERE buch_id='$buch_id'");
    $conn->query("DELETE FROM buecher WHERE buch_id='$buch_id'");
    redirect();
}
//Buch ausleihen
if (isset($_POST['borrow'])) {
    $buch_id = intval($_POST['buch_id']);
    $leser_id = intval($_POST['leser_id']);
    $datum = date("Y-m-d");
    //Buch ausleihen
    $conn->query("INSERT INTO ausleihen (buch_id, leser_id, bibliothekar_id, ausleihdatum, status)
                  VALUES ('$buch_id', '$leser_id', '{$_SESSION["admin_id"]}', '$datum', 'ausgeliehen')");
    redirect();
}
//Buch zurückgeben
if (isset($_GET['return'])) {
    $id = intval($_GET['return']);
    $heute = date("Y-m-d");
    //Leihstatus aktualisieren
    $conn->query("UPDATE ausleihen SET status='zurückgegeben', rueckgabedatum_ist='$heute'
                  WHERE ausleihe_id='$id'");
    redirect();
}
//Daten für Anzeige abrufen
$buecher = $conn->query("SELECT * FROM buecher ORDER BY titel ASC");
$leser_list = $conn->query("SELECT * FROM leser ORDER BY vorname ASC")->fetch_all(MYSQLI_ASSOC);
$ausgeliehen = $conn->query("
    SELECT a.ausleihe_id, b.titel, l.vorname, l.nachname
    FROM ausleihen a
    JOIN buecher b ON a.buch_id = b.buch_id
    JOIN leser l ON a.leser_id = l.leser_id
    WHERE a.status='ausgeliehen'
");
//Buch bearbeiten
$edit_id = $_GET['edit'] ?? null;
$edit_data = $edit_id ? $conn->query("SELECT * FROM buecher WHERE buch_id='" . intval($edit_id) . "'")->fetch_assoc() : null;
?>

<h1>Bücherverwaltung</h1>

<div class="manageBooks">
    <h3>Neues Buch hinzufügen</h3>

    <form method="post">
        <input name="isbn" placeholder="ISBN" required>
        <input name="titel" placeholder="Titel" required>
        <textarea name="beschreibung" placeholder="Beschreibung"></textarea>
        <input name="verlag" placeholder="Verlag">
        <input type="number" step="0.01" name="preis" placeholder="Anschaffungspreis" required>
        <input name="kategorie" placeholder="Kategorie">
        <button name="add">+</button>
    </form>
</div>

<?php if ($edit_data): ?>
    <div class="manageBooks">
        <h3>Buch bearbeiten</h3>

        <form method="post">
            <input name="buch_id" value="<?= $edit_data['buch_id'] ?>" readonly>
            <input name="isbn" value="<?= $edit_data['isbn'] ?>" readonly>
            <input name="titel" value="<?= $edit_data['titel'] ?>">
            <textarea name="beschreibung"><?= $edit_data['beschreibung'] ?></textarea>
            <input name="verlag" value="<?= $edit_data['verlag'] ?>">
            <input type="number" step="0.01" name="preis" value="<?= $edit_data['anschaffungspreis'] ?>">
            <input name="kategorie" value="<?= $edit_data['kategorie'] ?>">
            <button name="update">Speichern</button>
        </form>
    </div>
<?php endif; ?>

<div class="manageBooks">
    <h3>Bücherliste</h3>

    <table>
        <tr>
            <th>Buch ID</th>
            <th>ISBN</th>
            <th>Titel</th>
            <th>Verlag</th>
            <th>Kategorie</th>
            <th>Preis</th>
            <th>Aktionen</th>
        </tr>
        <?php
        //Bücher aus der Datenbank anzeigen
        while ($b = $buecher->fetch_assoc()): ?>
            <tr>
                <td><?= $b['buch_id'] ?></td>
                <td><?= $b['isbn'] ?></td>
                <td><?= $b['titel'] ?></td>
                <td><?= $b['verlag'] ?></td>
                <td><?= $b['kategorie'] ?></td>
                <td><?= $b['anschaffungspreis'] ?> €</td>
                <td>
                    <a href="?edit=<?= $b['buch_id'] ?>"><button>Bearbeiten</button></a>
                    <a href="?delete=<?= $b['buch_id'] ?>" onclick="return confirm('Löschen?')"><button
                            class="btn-danger">Löschen</button></a>

                    <?php
                    //Prüfen ob Buch ausgeliehen ist
                    $is_borrowed = $conn->query("SELECT COUNT(*) as count FROM ausleihen WHERE buch_id='{$b['buch_id']}' AND status='ausgeliehen'")->fetch_assoc()['count'] > 0;
                    if (!$is_borrowed): ?>
                        <form method="post" class="inline-form">
                            <input type="hidden" name="buch_id" value="<?= $b['buch_id'] ?>">
                            <select name="leser_id">
                                <?php foreach ($leser_list as $l): ?>
                                    <option value="<?= $l['leser_id'] ?>">
                                        <?= $l['vorname'] . " " . $l['nachname'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button name="borrow">Ausleihen</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<div class="manageBooks">
    <h3>Aktive Ausleihen</h3>

    <table>
        <tr>
            <th>Buch</th>
            <th>Leser</th>
            <th>Aktion</th>
        </tr>
        <?php while ($a = $ausgeliehen->fetch_assoc()): ?>
            <tr>
                <td><?= $a['titel'] ?></td>
                <td><?= $a['vorname'] . " " . $a['nachname'] ?></td>
                <td><a href="?return=<?= $a['ausleihe_id'] ?>"><button class="btn-danger">Zurückgeben</button></a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</main>

</body>

</html>

<?php $conn->close(); ?>