<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Noah Schwarz" />
    <link rel="stylesheet" href="../functions/style.css" />
    <title>TFS-Waxenberg Bibliothek</title>
  </head>
  <body>
    <div class="header">
      <div id="logo">
        <a href="main.php"><img src="../functions/images/..." alt="logo" /></a>
      </div>
      <div id="title">Login</div>
      <div class="menu">
        <button><a href="admin.php">Verwaltung</a></button>
        <button><a href="main.php">Home</a></button>
      </div>
    </div>

    <main>
        <div class="login-form"></div>
            <form action="login.php" method="post">
            <h2>Login</h2>
            <p>für Bibliothekare</p>
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Anmelden</button>
            </form>
        </div>
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
