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
}else{
    //Se non hai settato username guardo tuo profilo
    $username=getUserName($dbh);
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
    $templateParams["title"] = "Pagina profilo di " . $username;
    if($username===getUserName($dbh)){
        //E' il tuo profilo
        $templateParams["followDisabled"]=true;
        $templateParams["animalsDisabled"]=false;
        $templateParams["followersDisabled"]=false;
    }else{
        //E' il profilo di qualcun altro
        $templateParams["followDisabled"]=false;
        $templateParams["animalsDisabled"]=false;
        $templateParams["followersDisabled"]=false;
    }
    $ownsAnimals=doesUserOwnAnimals($username, $dbh);
    if($ownsAnimals==false){
        //Se non ha animali non posso premere il tasto
        //Altrimenti si, ma solo se utente esiste ecc... quindi seguo le regole sopra
        $templateParams["animalsDisabled"]=true;
    }
    
} else {
    //Non c'è l'utente che vuoi
    $templateParams["title"] = "Utente non esiste";
    $templateParams["username"] = "Utente non esiste";
    $templateParams["img"] = "#";
    $templateParams["role"] = "Utente non esiste";
    $templateParams["description"] = "Utente non esiste";
    $templateParams["followDisabled"]=true;
    $templateParams["animalsDisabled"]=true;
    $templateParams["followersDisabled"]=true;

}

$templateParams["page"] = "user-profile.php";
require_once("template/base.php");
