<?php
//header einbinden
include '../functions/header.php'; ?>

<?php
//Datenbankverbindung herstellen
$conn = new mysqli("localhost", "root", "10032008", "bibliothek_mtsp");
$conn->set_charset("utf8");

$fehler = "";
//Überprüfen ob Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string($_POST["email"]);
    $passwort = hash("sha256", $_POST["passwort"]);
    //SQL Abfrage zum Überprüfen der Anmeldedaten
    $sql = "SELECT * FROM bibliothekare WHERE email='$email' AND passwort_hash='$passwort'";
    $result = $conn->query($sql);
    //falls Anmeldedaten korrekt sind
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        //Session-Variablen setzen
        $_SESSION["admin_id"] = $admin["bibliothekar_id"];
        $_SESSION["admin_name"] = $admin["nachname"];
        $_SESSION["admin_vname"] = $admin["vorname"];
        //Weiterleitung zur Startseite
        header("Location: main.php");
        exit;
    } else {
        //falls Anmeldedaten falsch sind
        $fehler = "Falsche Anmeldedaten";
    }
}
?>

<h1>Admin Login</h1>

<?php if ($fehler): ?>
    <p class="muted center"><?= $fehler ?></p>
<?php endif; ?>

<form method="post" class="login-form">
    <label>E-Mail</label>
    <input type="email" name="email" required>

    <label>Passwort</label>
    <input type="password" name="passwort" required>

    <div class="form-actions">
        <button>Login</button>
    </div>
</form>

</main>

</body>

</html>