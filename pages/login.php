<?php
session_start();
//header einbinden
include '../functions/header.php'; ?>

<?php
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "10032008", "bibliothek_mtsp");
$conn->set_charset("utf8");
$fehler = "";

//Überprüfen ob Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $benutzername = $conn->real_escape_string($_POST["benutzername"]);
    $passwort = hash("sha256", $_POST["passwort"]);
    //SQL Abfrage zum Überprüfen der Anmeldedaten
    $sql = "SELECT * FROM bibliothekare WHERE benutzername='$benutzername' AND passwort_hash='$passwort'";
    $result = $conn->query($sql);
    //wann korrekt
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        //Session Var setzen
        $_SESSION["admin_id"] = $admin["bibliothekar_id"];
        $_SESSION["admin_name"] = $admin["nachname"];
        $_SESSION["admin_vname"] = $admin["vorname"];
        //Weiterleitung Startseite
        header("Location: main.php");
        exit;
    } else {
        //falls falsch
        $fehler = "Falsche Anmeldedaten";
    }
}
?>

<h1>Admin Login</h1>

<?php if ($fehler): ?>
    <p class="muted center"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" class="login-form">
    <label>Benutzername</label>
    <input type="text" name="benutzername" required>

    <label>Passwort</label>
    <input type="password" name="passwort" required>

    <div class="form-actions">
        <button>Login</button>
    </div>
</form>

</main>

</body>

</html>