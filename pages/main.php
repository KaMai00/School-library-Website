<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Noah Schwarz">
    <link rel="stylesheet" href="../functions/style.css">
    <title>TFS-Waxenberg Bibliothek</title>
</head>

<body>

    <div class="header">
        <div id="logo">
            <a href="main.php"><img src="../functions/images/..." alt="logo" /></a>
        </div>
        <div id="title">Willkommen zu Bibliotheks Website</div>
        <div class="menu">
            <button><a href="admin.php">Verwaltung</a></button>
            <button><a href="login.php">Login</a></button>
        </div>
    </div>


    <main>
        <div class="search">
            <form method="get" action="main.php">
                <input type="text" name="q" placeholder="Buch suchen..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button type="submit">Suchen</button>
            </form>
        </div>

        <?php
        session_start();
        require __DIR__ . '/../functions/db.php';

        $q = trim((string)($_GET['q'] ?? ''));
        if ($q !== '') {
            $term = '%' . $q . '%';
            // Search in titel, isbn, kategorie, verlag, beschreibung
            $stmt = $pdo->prepare('SELECT * FROM buecher WHERE titel LIKE :t OR isbn LIKE :t OR kategorie LIKE :t OR verlag LIKE :t OR beschreibung LIKE :t ORDER BY titel');
            $stmt->execute([':t' => $term]);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query('SELECT * FROM buecher ORDER BY titel LIMIT 50');
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (!$books) {
            echo '<p>Keine Ergebnisse gefunden.</p>';
        } else {
            echo '<div class="books">';
            foreach ($books as $b) {
                echo '<div class="book">';
                echo '<img src="../functions/images/..." alt="book-cover">';
                echo '<div class="book-info">';
                echo '<h3>' . htmlspecialchars($b['titel']) . '</h3>';
                echo '<p>Verlag: ' . htmlspecialchars($b['verlag'] ?? '') . '</p>';
                echo '<p>Kategorie: ' . htmlspecialchars($b['kategorie'] ?? '') . '</p>';
                echo '<p>Beschreibung: ' . htmlspecialchars($b['beschreibung'] ?? '') . '</p>';
                echo '<p>ISBN: ' . htmlspecialchars($b['isbn']) . '</p>';
                echo '</div></div>';
            }
            echo '</div>';
        }
        ?>
    </main>


    <footer>
        <p>© 2025 TFS-Waxenberg Bibliothek. Alle Rechte vorbehalten.</p>
        <div class="menu">
            <button><a href="subpages/security-notice.php">Datenschutz</a></button>
            <button><a href="subpages/impressum.php">impressum</a></button>
        </div>
    </footer>
</body>

</html>