<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home

if (isUserLoggedIn($dbh)==false) {
    header("Location: login.php");
    exit;
}
#Altrimenti può guardare un utente
#Carico i dati utente da db
$user=getUserData(getUserName($dbh), $dbh);
if(empty($user)==false){
    $templateParams["username"]=$user["username"];
    $templateParams["img"]=IMG_DIR . $user["immagine"];
    $templateParams["role"]=$user["impiego"];
    $templateParams["description"]=$user["descrizione"];
}
#TODO: Error

$templateParams["page"] = "user-profile.php";
$templateParams["title"] = "Titolo";
require_once("template/base.php");
?>