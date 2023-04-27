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

if (empty($_POST)) {
    //Non è stato inviato nulla per post, probabilmente viene fatto accesso alla pagina direttamente
} else {
    if (isset($_POST["imgpost"]) && isset($_POST["imgalt"]) && isset($_POST["txtpost"])) {
        $img = $_POST["imgpost"];
        $ext = pathinfo($img, PATHINFO_EXTENSION);
        if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif") {
            //TODO: Salvala nel database?
            $alt = $_POST["imgalt"];
            $text = $_POST["txtpost"];

            if (strlen($alt) <= 50 && strlen($text) <= 100) {
                //TODO: Effettua query al db
                //Se query va a buon fine, redirect a index?
            } else {
                $templateParams["error"] = "La descrizione dell'immagine deve essere di meno di 50 caratteri e il testo meno di 100 caratteri";
            }
        }else{
            $templateParams["error"] = "Compila tutti i campi e metti un'immagine con estensione jpg, jpeg, png o gif";
        }
    } else {
        $templateParams["error"] = "Compila tutti i campi e metti un'immagine con estensione jpg, jpeg, png o gif";
    }
}


$templateParams["page"] = "new-post.php";
$templateParams["title"] = "Crea nuovo post";
require_once("template/base.php");
