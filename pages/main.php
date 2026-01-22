<?php
//header einbinden
include '../functions/header.php'; ?>

<h1>Bibliothek Startseite</h1>

<form method="post" class="search">
    <input type="text" name="suche" placeholder="Nach Titel, Verlag oder Kategorie suchen..." required>
    <button>Suchen</button>
</form>

<?php
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "10032008", "bibliothek_mtsp");
$conn->set_charset("utf8");
if ($conn->connect_error)
    die("DB Fehler: " . $conn->connect_error);


//Suchfunktion
if (isset($_POST['suche'])) {
    $suche = $conn->real_escape_string($_POST['suche']);
    echo "<h2>Suchergebnisse:</h2>";
    $sql = "SELECT * FROM buecher WHERE titel LIKE '%$suche%' OR verlag LIKE '%$suche%' OR kategorie LIKE '%$suche%'";
} else {
    echo "<h2>Alle Bücher:</h2>";
    $sql = "SELECT * FROM buecher ORDER BY titel ASC";
}

$result = $conn->query($sql);
?>


<?php
//Bücher aus der Datenbank anzeigen
if ($result && $result->num_rows > 0): ?>
    <div class="books">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="book">
                <img src="book-placeholder.png" alt="Buchcover">
                <div class="book-info">
                    <h3><?= $row['titel'] ?></h3>
                    <p>ISBN: <?= $row['isbn'] ?></p>
                    <p>Verlag: <?= $row['verlag'] ?></p>
                    <p>Kategorie: <?= $row['kategorie'] ?></p>
                    <p>Preis: <?= $row['anschaffungspreis'] ?> €</p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>Keine Bücher gefunden.</p>
<?php endif; ?>


</main>

</body>

</html>