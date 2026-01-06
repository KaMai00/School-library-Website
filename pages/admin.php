<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Noah Schwarz" />
    <link rel="stylesheet" href="../functions/style.css" />
    <title>TFS-Waxenberg Bibliothek</title>
  </head>
    <?php
    session_start();
    require __DIR__ . '/../functions/db.php';

    // require admin
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo '<p>Nur für Administratoren. Bitte <a href="login.php">anmelden</a>.</p>';
        exit;
    }

    // Handle POST actions: add/update/delete books (`buecher`) and librarians (`bibliothekare`)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'add_book') {
        $ins = $pdo->prepare('INSERT INTO buecher (titel, isbn, beschreibung, verlag, anschaffungspreis, kategorie) VALUES (:titel, :isbn, :beschreibung, :verlag, :anschaffungspreis, :kategorie)');
        $ins->execute([
          ':titel' => $_POST['title'] ?? '',
          ':isbn' => $_POST['isbn'] ?? '',
          ':beschreibung' => $_POST['description'] ?? '',
          ':verlag' => $_POST['verlag'] ?? '',
          ':anschaffungspreis' => $_POST['anschaffungspreis'] ?: null,
          ':kategorie' => $_POST['category'] ?? ''
        ]);
            header('Location: admin.php'); exit;
        }
        if ($action === 'delete_book' && !empty($_POST['book_id'])) {
        $del = $pdo->prepare('DELETE FROM buecher WHERE buch_id = :id');
        $del->execute([':id' => (int)$_POST['book_id']]);
            header('Location: admin.php'); exit;
        }
        if ($action === 'update_book' && !empty($_POST['book_id'])) {
        $up = $pdo->prepare('UPDATE buecher SET titel=:titel, isbn=:isbn, beschreibung=:beschreibung, verlag=:verlag, anschaffungspreis=:anschaffungspreis, kategorie=:kategorie WHERE buch_id=:id');
        $up->execute([
          ':titel' => $_POST['title'] ?? '',
          ':isbn' => $_POST['isbn'] ?? '',
          ':beschreibung' => $_POST['description'] ?? '',
          ':verlag' => $_POST['verlag'] ?? '',
          ':anschaffungspreis' => $_POST['anschaffungspreis'] ?: null,
          ':kategorie' => $_POST['category'] ?? '',
          ':id' => (int)$_POST['book_id']
        ]);
            header('Location: admin.php'); exit;
        }
      if ($action === 'add_user') {
        $vorname = trim((string)($_POST['vorname'] ?? ''));
        $nachname = trim((string)($_POST['nachname'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $benutzername = trim((string)($_POST['benutzername'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        if ($benutzername !== '' && $password !== '') {
          $hash = password_hash($password, PASSWORD_DEFAULT);
          $ins = $pdo->prepare('INSERT INTO bibliothekare (vorname, nachname, email, benutzername, passwort_hash, aktiv) VALUES (:v, :n, :e, :b, :p, 1)');
          try {
            $ins->execute([':v'=>$vorname, ':n'=>$nachname, ':e'=>$email, ':b'=>$benutzername, ':p'=>$hash]);
          } catch (Exception $e) {
            // ignore unique constraint errors for now
          }
        }
        header('Location: admin.php'); exit;
      }
      if ($action === 'delete_user' && !empty($_POST['user_id'])) {
        $del = $pdo->prepare('DELETE FROM bibliothekare WHERE bibliothekar_id = :id');
        $del->execute([':id' => (int)$_POST['user_id']]);
        header('Location: admin.php'); exit;
      }
      if ($action === 'update_user' && !empty($_POST['user_id'])) {
        $up = $pdo->prepare('UPDATE bibliothekare SET vorname=:v, nachname=:n, email=:e, benutzername=:b, aktiv=:a WHERE bibliothekar_id=:id');
        $up->execute([':v'=>$_POST['vorname']??'', ':n'=>$_POST['nachname']??'', ':e'=>$_POST['email']??'', ':b'=>$_POST['benutzername']??'', ':a'=>isset($_POST['aktiv'])?1:0, ':id'=>(int)$_POST['user_id']]);
        if (!empty($_POST['password'])) {
          $pw = password_hash($_POST['password'], PASSWORD_DEFAULT);
          $pdo->prepare('UPDATE bibliothekare SET passwort_hash = :p WHERE bibliothekar_id = :id')->execute([':p' => $pw, ':id' => (int)$_POST['user_id']]);
        }
        header('Location: admin.php'); exit;
      }
    }

    // Fetch librarians and books for display using existing schema
    $users = $pdo->query('SELECT bibliothekar_id, vorname, nachname, email, benutzername, aktiv FROM bibliothekare ORDER BY benutzername')->fetchAll(PDO::FETCH_ASSOC);
    $books = $pdo->query('SELECT * FROM buecher ORDER BY titel')->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <div class="header">
      <div id="logo">
        <a href="main.php"><img src="../functions/images/..." alt="logo" /></a>
      </div>
      <div id="title">Verwaltung</div>
      <div class="menu">
        <button><a href="admin.php">Verwaltung</a></button>
        <button><a href="main.php">Home</a></button>
      </div>
    </div>

    <main>
      <h2>Admin-Bereich</h2>
      <p>Willkommen im Verwaltungsbereich. Hier können Sie Bücher verwalten und Benutzerkonten erstellen.</p>

      <section class="manageBooks">
        <h3>Bücher hinzufügen</h3>
        <form action="admin.php" method="post">
          <input type="hidden" name="action" value="add_book">
          <label>Titel: <input name="title" required></label><br>
          <label>Autor: <input name="author"></label><br>
          <label>ISBN: <input name="isbn"></label><br>
          <label>Kategorie: <input name="category"></label><br>
          <label>Beschreibung: <input name="description"></label><br>
          <button type="submit">Buch hinzufügen</button>
        </form>

        <h4>Bestehende Bücher</h4>
        <?php foreach ($books as $b): ?>
          <div style="border:1px solid #ccc;padding:8px;margin:6px 0;">
            <form method="post" action="admin.php">
              <input type="hidden" name="action" value="update_book">
              <input type="hidden" name="book_id" value="<?php echo (int)$b['id']; ?>">
              <label>Titel: <input name="title" value="<?php echo htmlspecialchars($b['title']); ?>"></label><br>
              <label>Autor: <input name="author" value="<?php echo htmlspecialchars($b['author']); ?>"></label><br>
              <label>ISBN: <input name="isbn" value="<?php echo htmlspecialchars($b['isbn']); ?>"></label><br>
              <label>Kategorie: <input name="category" value="<?php echo htmlspecialchars($b['category']); ?>"></label><br>
              <label>Beschreibung: <input name="description" value="<?php echo htmlspecialchars($b['description']); ?>"></label><br>
              <button type="submit">Speichern</button>
            </form>
            <form method="post" action="admin.php" style="display:inline;">
              <input type="hidden" name="action" value="delete_book">
              <input type="hidden" name="book_id" value="<?php echo (int)$b['id']; ?>">
              <button type="submit" onclick="return confirm('Wirklich löschen?')">Löschen</button>
            </form>
          </div>
        <?php endforeach; ?>
      </section>

      <section class="manageUsers">
        <h3>Benutzer hinzufügen</h3>
        <form action="admin.php" method="post">
          <input type="hidden" name="action" value="add_user">
          <label>Benutzername: <input name="username" required></label><br>
          <label>Passwort: <input type="password" name="password" required></label><br>
          <label>Rolle: <select name="role"><option value="user">user</option><option value="admin">admin</option></select></label><br>
          <button type="submit">Benutzer erstellen</button>
        </form>

        <h4>Bestehende Benutzer</h4>
        <?php foreach ($users as $u): ?>
          <div style="border:1px solid #ccc;padding:8px;margin:6px 0;">
            <form method="post" action="admin.php">
              <input type="hidden" name="action" value="update_user">
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
              <label>Benutzername: <input name="username" value="<?php echo htmlspecialchars($u['username']); ?>"></label>
              <label>Rolle: <select name="role"><option value="user" <?php echo $u['role']==='user'?'selected':''; ?>>user</option><option value="admin" <?php echo $u['role']==='admin'?'selected':''; ?>>admin</option></select></label>
              <label>Neues Passwort: <input type="password" name="password"></label>
              <button type="submit">Speichern</button>
            </form>
            <form method="post" action="admin.php" style="display:inline;">
              <input type="hidden" name="action" value="delete_user">
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
              <button type="submit" onclick="return confirm('Benutzer löschen?')">Löschen</button>
            </form>
          </div>
        <?php endforeach; ?>
      </section>
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
