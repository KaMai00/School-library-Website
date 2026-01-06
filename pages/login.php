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
    <?php
    session_start();
    require __DIR__ . '/../functions/db.php';

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim((string)($_POST['username'] ?? ''));
      $password = (string)($_POST['password'] ?? '');
      if ($username === '' || $password === '') {
        $error = 'Bitte Benutzername und Passwort eingeben.';
      } else {
        // Authenticate against `bibliothekare` table
        $stmt = $pdo->prepare('SELECT * FROM bibliothekare WHERE benutzername = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['passwort_hash'])) {
          // login success - treat bibliothekare as admins
          $_SESSION['user'] = ['id' => $user['bibliothekar_id'], 'username' => $user['benutzername'], 'role' => 'admin'];
          header('Location: admin.php');
          exit;
        }
        $error = 'Ungültige Anmeldeinformationen.';
      }
    }

    // logout handling
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
      session_unset();
      session_destroy();
      header('Location: login.php');
      exit;
    }

    ?>
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
        <?php if ($error): ?>
          <p style="color:crimson"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
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
