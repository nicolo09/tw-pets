<?php
require_once("bootstrap.php");

# Se l'utente è già loggato, viene reindirizzato alla home
if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

if (isset($_GET["id"])) {
    if (isPasswordResetCodeValid($_GET["id"], $dbh)) {
        //Il codice è valido
        $code = $_GET["id"];
        //Permetti il reset della password
        $email=$dbh->getResetCodeInfo($code)["email"];
        $username = $dbh->getUser($email)[0]["username"];
        $templateParams["username"] = $username;
        //Il codice è valido e sto cercando di cambiare password
        if (isset($_POST["new-password"], $_POST["new-password-repeat"])) {
            $new_password = $_POST["new-password"];
            $confirm_password = $_POST["new-password-repeat"];
            $result = changePasswordReset($username, $new_password, $confirm_password, $dbh);
            if ($result[0] == true) {
                // Password cambiata con successo
                // Rimuovo tutti i codici generati per cambiare la password
                removeAllPasswordChangeRequests($email, $dbh);
                //TODO: send mail
                header("Location: login.php?password_changed=true");
                exit;
            } else {
                // Cambio password fallito
                $templateParams["errors"] = $result[1];
            }
        }
    } else {
        //Il codice non è valido
        $_SESSION["error"] = "Il codice non è più valido. Potrebbe essere scaduto, oppure ne è stato richiesto un altro. Riprova";
        header("Location: reset-password.php");
        exit;
    }
}

# Se accedi direttamente alla pagina
$templateParams["title"] = "PETS - Cambia la password";
$templateParams["page"] = "reset-page.php";
require_once("template/base-outside.php");

?>
