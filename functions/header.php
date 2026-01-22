<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="David Danninger">
    <title>Bibliothek</title>

    <link rel="stylesheet" href="../functions/style.css?v=3">
</head>

<body>

    <header class="header">
        <div id="logo">
            <img src="logo.png" alt="Bibliothek Logo">
        </div>
        <h1 id="title">Bibliothek MTSP</h1>
        <nav class="menu">
            <a href="main.php"><button>Startseite</button></a>
            <a href="admin.php"><button>Bücherverwaltung</button></a>
            <?php if (isset($_SESSION["admin_id"])): ?>
                <span class="muted">Eingeloggt als: <?= $_SESSION["admin_vname"] . " " . $_SESSION["admin_name"] ?></span>
                <a href="logout.php"><button>Logout</button></a>
            <?php else: ?>
                <a href="login.php"><button>Login</button></a>
            <?php endif; ?>
        </nav>
    </header>

    <main>