<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home
if (isset($_SESSION["user"])) {
    header("Location: home/home.php");
    exit;
}

# Se è stato inviato il form di login


?>