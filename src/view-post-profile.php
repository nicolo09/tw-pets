<?php
require_once("bootstrap.php");

# Se l'utente non è già loggato, viene reindirizzato alla home

if (login_check($dbh) == false) {
    header("Location: login.php");
    exit;
}

$id=-1;
if(isset($_GET["id"])){
    $id=$_GET["id"];
}

$result=getPost($id, $dbh);
if(empty($result)==false){
    //It's not empty
    $templateParams["id"]=$id;
    $templateParams["immagine"]=IMG_DIR.$result["immagine"];
    $templateParams["alt"]=$result["alt"];
    $templateParams["descrizione"]=$result["testo"];
    $templateParams["timestamp"]=date( "d/m/Y H:i", strtotime($result["timestamp"]));
    $templateParams["username"]=$result["username"];
    $templateParams["immagineprofilo"]=IMG_DIR.$result["immagineprofilo"];
    $templateParams["title"]="Post di ".$templateParams["username"];
    $templateParams["animals"]=getAnimalsInPost($id, $dbh);
    //Comments are loaded by javascript
}else{
    //Post doesn't exist
    header("Location: tab-profile.php");
    exit;
}

$templateParams["page"] = "post.php";
require_once("template/base.php");