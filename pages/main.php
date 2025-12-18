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
            <a href="main.html"><img src="../functions/images/..." alt="logo" /></a>
        </div>
        <div id="title">Willkommen zu Bibliotheks Website</div>
        <div class="menu">
            <button><a href="admin.html">Verwaltung</a></button>
            <button><a href="login.html">Login</a></button>
        </div>
    </div>


    <main>
        <div class="search">
            <input type="text" placeholder="Buch suchen...">
            <button>Suchen</button>
        </div>

        <?php
        $book = "1984";
        $kategorie = "kategorie";
        $beschreibung = "Big Brother is watching you.";

        echo '
            <div class="books">
                <div class="book">
                    <img src="../functions/images/..." alt="book-cover">
                    <div class="book-info">
                        <h3>' . $book . '</h3>
                        <p>Kategorie: ' . $kategorie . '</p>
                        <p>Beschreibung: ' . $beschreibung . '</p>
                        <button>Mehr erfahren</button>
                    </div>
                </div>
            </div>';
        ?>
    </main>


    <footer>
        <p>© 2025 TFS-Waxenberg Bibliothek. Alle Rechte vorbehalten.</p>
        <div class="menu">
            <button><a href="subpages/security-notice.html">Datenschutz</a></button>
            <button><a href="subpages/impressum.html">impressum</a></button>
        </div>
    </footer>
</body>

</html>