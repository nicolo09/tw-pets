<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home

if (isUserLoggedIn($dbh)==false) {
    header("Location: login.php");
    exit;
}
#Altrimenti può guardare un utente

$templateParams["page"] = "user-profile.php";
$templateParams["title"] = "Titolo";
require_once("template/base.php");
?>