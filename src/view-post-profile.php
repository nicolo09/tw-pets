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
    $templateParams["id"]=$id;
    $templateParams["immagine"]=IMG_DIR.$result["immagine"];
    $templateParams["alt"]=$result["alt"];
    $templateParams["descrizione"]=$result["testo"];
    $templateParams["timestamp"]=date( "d/m/Y H:i", strtotime($result["timestamp"]));
    $templateParams["username"]=$result["username"];
    $templateParams["immagineprofilo"]=IMG_DIR.$result["immagineprofilo"];
    $templateParams["title"]="Post di ".$templateParams["username"];
    if($result["username"]==getUserName($dbh)){
        //E' un tuo post, disabilito i bottoni
        $templateParams["disableLike"]=true;
        $templateParams["disableSave"]=true;
    }else{
        $templateParams["disableLike"]=false;
        $templateParams["disableSave"]=false;
    }
    $templateParams["animals"]=getAnimalsInPost($id, $dbh);
    //Carico 5 commenti (i più recenti)
    $n=5;
    $comments=loadMostRecentComments($id, $n, $dbh);
    $templateParams["canLoadMoreComments"]=true;
    $templateParams["comments"]=$comments;
    $all=allLoadMostRecentComments($id, $dbh);
    if(count($all)>count($templateParams["comments"])){
        $templateParams["more-comments"]=true;
    }else{
        $templateParams["more-comments"]=false;
    }
    //I commenti che mostro hanno risposte?
    foreach($comments as $comm){
        $templateParams["son-comments-".$comm["id_commento"]]=doesCommentHaveComments($comm["id_commento"], $dbh);
    }
}else{
    //Post non esiste, redirect
    header("Location: tab-profile.php");
    exit;
}

$templateParams["page"] = "post.php";
require_once("template/base.php");