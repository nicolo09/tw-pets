<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home

if (isUserLoggedIn($dbh) == false) {
    header("Location: login.php");
    exit;
}
#Altrimenti può guardare un utente
#Carico i dati utente da db
$username = "";
if (isset($_GET["username"])) {
    if (doesPersonUsernameExist($_GET["username"], $dbh) == 1) {
        $username = $_GET["username"];
    }
}


if (empty($username) == false) {
    $user = getUserData($username, $dbh);
    if (empty($user) == false) {
        $templateParams["username"] = $user["username"];
        $templateParams["img"] = "img/" . $user["immagine"];
        $templateParams["role"] = $user["impiego"];
        $templateParams["description"] = $user["descrizione"];
    }
    $posts = getUserCreatedPosts($username, $dbh);
    if (empty($posts) == false) {
        foreach ($posts as $single) {
            $templateParams["postimg"][] = "img/" . $single["immagine"];
            $templateParams["alt"][] = $single["alt"];
        }
    }
} else {
    //Non c'è l'utente che vuoi
    #TODO: Error
}

$templateParams["page"] = "user-profile.php";
$templateParams["title"] = "Pagina profilo di " . $username;
require_once("template/base.php");
