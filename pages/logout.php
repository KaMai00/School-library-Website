<?php
//Session start
session_start();

//Session Var löschen
$_SESSION = [];

//Session end
session_destroy();

//Weiterleitung Startseite
header("Location: main.php");
exit;
