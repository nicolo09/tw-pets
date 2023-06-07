<?php
require_once("bootstrap.php");

if (login_check($dbh)) {
    header("Location: home.php");
    exit;
}

if (isset($_POST['username'])) {
    $username = "";
    $email= "";
    $result = doesPersonUsernameExist($_POST['username'], $dbh);
    if ($result == false) {
        // The user doesn't exist, it may be an email address
        $resultMail = $dbh->getUser($_POST['username']);
        if (empty($resultMail) == false) {
            //Retrieved the username from the email
            $username = $resultMail[0]['username'];
            $email=$_POST['username'];
        }
    } else {
        $username = $_POST['username'];
        $email=getUserData($username, $dbh)['email'];
    }

    if ($username != ""&&$email!="") {
        $code=createResetCode($email, $dbh);
        $outcome=sendResetEmail($email, $code);
        if($outcome==false){
            //There was an error while sending the email
            $_SESSION["error"] = "C'è stato un errore nell'invio della mail";
        }
        }

    $_SESSION["message"] = "Ti è stato inviata una mail per cambiare la password. Controlla la tua casella di posta elettronica";
    header("Location: login.php" );
    exit;
}

if(!empty($_SESSION["error"])) {
    $templateParams["errors"] = array($_SESSION["error"]);
    unset($_SESSION["error"]);
}

if(!empty($_SESSION["message"])) {
    $templateParams["messages"] = array($_SESSION["message"]);
    unset($_SESSION["message"]);
}
// User opened the page
$templateParams["title"] = "PETS - Hai dimenticato la password";
$templateParams["page"] = "reset-password-template.php";
require_once("template/base-outside.php");

?>
