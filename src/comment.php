<?php
require_once("bootstrap.php");

if (isUserLoggedIn($dbh) == false) {
    header("Location: login.php");
    exit;
}

//Se l'utente è loggato
$id_post = -1;
$id_padre = -1;
$text="";
$success = 1;
if (isset($_GET["id_post"])) {
    $id_post = $_GET["id_post"];
}
if (isset($_GET["id_padre"])) {
    $id_padre = $_GET["id_padre"];
}
if (isset($_GET["text"])) {
    $text = $_GET["text"];
}

//Se l'id è valido
if (isIdPostValid($id_post, $dbh)) {
    //Se esiste il commento padre ed è dello stesso post che vuoi fare tu il commento
    //Post valido
    $padre_comment=getCommentInfo($id_padre, $dbh);
    if(empty($padre_comment)){
        //Commento al post 
        newComment(getUserName($dbh), $text, $id_post, $dbh);
    }else{
        //Commento al commento padre
        if($padre_comment["id_post"]==$id_post){
            //Faccio il commento
            newCommentAnswer(getUserName($dbh), $id_padre, $text, $id_post, $dbh);
        }
    }
} else {
    //Redirect a pagina precedente, success=0
    $success = 0;
}

//Ritorna a pagina post, success=0/1
header("Location: view-post-profile.php?id=" . $id . "&successL=" . $success);
