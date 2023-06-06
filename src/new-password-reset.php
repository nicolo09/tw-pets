<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home
if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

//http://localhost/tw-pets/new-password-reset.php?id=".$code."
if(isset($_GET["id"])){
    if(isPasswordResetCodeValid($_GET["id"], $dbh)){
        //Il codice è valido
        $code=$_GET["id"];
        //Permetti il reset della password
        $username=$dbh->getUser($dbh->getResetCodeInfo($code)["email"])[0]["username"];
        $templateParams["username"]=$username;
        
    }else{
        //Il codice non è valido
        $_SESSION["error"] = "Il codice non è più valido. Potrebbe essere scaduto, oppure ne è stato richiesto un altro. Riprova";
        header("Location: reset-password.php");
        exit;
    }
}

# Se accedi direttamente alla pagina
$templateParams["title"] = "PETS - Hai dimenticato la password";
$templateParams["page"] = "reset-page.php";
require_once("template/base-outside.php");

?>
