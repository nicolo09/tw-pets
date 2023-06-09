<?php
require_once("bootstrap.php");

if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

if (isset($_GET["id"])) {
    if (isPasswordResetCodeValid($_GET["id"], $dbh)) {
        //The reset code is valid
        $code = $_GET["id"];
        //Granting password reset
        $email=$dbh->getResetCodeInfo($code)["email"];
        $username = $dbh->getUser($email)[0]["username"];
        $templateParams["username"] = $username;
        if (isset($_POST["new-password"], $_POST["new-password-repeat"])) {
            //The code is valid and the user is trying to change the password
            $new_password = $_POST["new-password"];
            $confirm_password = $_POST["new-password-repeat"];
            $result = changePasswordReset($username, $new_password, $confirm_password, $dbh);
            if ($result[0] == true) {
                // Password changed successfully
                // Deleting all reset codes previously generated
                removeAllPasswordChangeRequests($email, $dbh);
                sendEmailAboutPasswordChange($username, $dbh);
                //Account gets re-enabled
                $dbh->deleteAllLoginAttempts($username);
                $dbh->enableAccount($username);
                header("Location: login.php?password_changed=true");
                exit;
            } else {
                // Couldn't change the password
                $templateParams["errors"] = $result[1];
            }
        }
    } else {
        //The reset code is invalid
        $_SESSION["error"] = "Il codice non è più valido. Potrebbe essere scaduto, oppure ne è stato richiesto un altro. Riprova";
        header("Location: reset-password.php");
        exit;
    }
} else {
    $_SESSION["error"] = "Codice di reset mancante.";
    header("Location: reset-password.php");
    exit;
}

$templateParams["title"] = "PETS - Cambia la password";
$templateParams["page"] = "template/reset-page.php";
require_once("template/base-outside.php");

?>
