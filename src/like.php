<?php
require_once("bootstrap.php");

if (isUserLoggedIn($dbh) == false) {
    header("Location: login.php");
    exit;
}

//Se l'utente è loggato
$id = -1;
$success = 1;
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

//Se l'id è valido
if (isIdPostValid($id, $dbh)) {
    //Post valido
    if (isPostLikedBy($id, getUserName($dbh), $dbh)==false) {
        //All'utente piace il post
        $out = likePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    } else {
        //All'utente non piace il post
        $out = unLikePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    }
} else {
    //Redirect a pagina precedente, success=0
    $success = 0;
}

//Ritorna a pagina post, success=0/1
header("Location: view-post-profile.php?id=".$id."&successL=".$success);
?>
