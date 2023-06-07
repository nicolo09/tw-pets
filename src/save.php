<?php
require_once("bootstrap.php");

if (login_check($dbh) == false) {
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
    if (isPostSavedBy($id, getUserName($dbh), $dbh)==false) {
        //L'utente salva il post
        $out = savePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    } else {
        //L'utente non salva il post
        $out = unSavePost($id, getUserName($dbh), $dbh);
        if ($out == false) {
            $success = 0;
        }
    }
} else {
    //Redirect a pagina precedente, success=0
    $success = 0;
}

//Ritorna a pagina post, success=0/1
header("Location: view-post-profile.php?id=".$id."&successS=".$success);
?>