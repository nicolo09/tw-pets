<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home
if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

# Se è stato inviato il form di login
if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (loginUser($username, $password, $dbh) == true) {
        // Login eseguito con successo
        header("Location: home.php");
        exit;
    } else {
        // Login fallito
        header("Location: login.php?error=1");
        exit;
    }
}

$templateParams["page"] = "login-form.php";
require_once("template/base-outside.php");

?>
