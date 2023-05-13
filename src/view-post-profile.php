<?php
require_once("bootstrap.php");

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
}else{
//E' vuoto
    $templateParams["immagine"]="#";
    $templateParams["alt"]="Post non esiste";
    $templateParams["descrizione"]="Post non esiste";
    $templateParams["timestamp"]="Post non esiste";
    $templateParams["title"]="Post non esiste";
}

$templateParams["page"] = "post.php";
require_once("template/base.php");