<?php
require_once("bootstrap.php");

# Se l'utente non è già loggato, viene reindirizzato alla home

if (isUserLoggedIn($dbh) == false) {
    header("Location: login.php");
    exit;
}

$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$result=getPost($id, $dbh);
if(empty($result)==false){
    //Non è vuoto
    $templateParams["immagine"]=IMG_DIR.$result["immagine"];
    $templateParams["alt"]=$result["alt"];
    $templateParams["descrizione"]=$result["testo"];
    $templateParams["timestamp"]=$result["timestamp"];
    $templateParams["username"]=$result["username"];
    $templateParams["immagineprofilo"]=IMG_DIR.$result["immagineprofilo"];
    $templateParams["title"]="Post di ".$templateParams["username"];
    $likes=getLikes($id, $dbh);
    $isLiked=isPostLikedBy($id, getUserName($dbh), $dbh);
    $templateParams["nlikes"]=$likes;
    $templateParams["liked"]=$isLiked;
    $isSaved=isPostSavedBy($id, getUserName($dbh), $dbh);
    $templateParams["saved"]=$isSaved;
}else{
//E' vuoto
    $templateParams["immagine"]="#";
    $templateParams["alt"]="Post non esiste";
    $templateParams["descrizione"]="Post non esiste";
    $templateParams["timestamp"]="Post non esiste";
    $templateParams["title"]="Post non esiste";
    $templateParams["nlikes"]=-1; //Se il post non esiste, non esiste neanche il numero di like e quindi con -1 disabilito i bottoni
}

$templateParams["page"] = "post.php";
require_once("template/base.php");