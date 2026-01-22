<?php
//Session starten
session_start();

//Alle Session-Variablen löschen
$_SESSION = [];

//Session zerstören
session_destroy();

//Weiterleitung zur Startseite nach dem Logout
header("Location: main.php");
exit;
