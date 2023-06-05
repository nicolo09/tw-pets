<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home
if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

if (isset($_POST['username'])) {
    $username = "";
    $result = doesPersonUsernameExist($_POST['username'], $dbh);
    if ($result == false) {
        //L'username inserito non esiste, provo a controllare se è una mail
        $resultMail = $dbh->getUser($_POST['username']);
        if (empty($resultMail) == false) {
            //Ho recuperato lo username dalla mail
            $username = $resultMail[0]['username'];
        }
    } else {
        $username = $_POST['username'];
    }

    if ($username != "") {
        //TODO:Invio una mail
    }

    $_SESSION["message"] = "Ti è stato inviata una mail per cambiare la password. Controlla la tua casella di posta elettronica";
    header("Location: login.php" );
    exit;
}
# Se accedi direttamente alla pagina
$templateParams["title"] = "PETS - Hai dimenticato la password";
$templateParams["page"] = "reset-password-template.php";
require_once("template/base-outside.php");

?>
