<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home
/*
if (login_check($dbh)==false) {
    header("Location: login.php");
    exit;
}
 */
//TODO: Dopo che il login è a posto, decommenta
#Altrimenti può creare un nuovo post

$templateParams["page"] = "new-post.php";
$templateParams["title"] = "Crea nuovo post";
require_once("template/base.php");

?>
