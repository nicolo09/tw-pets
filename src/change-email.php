<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if (isset($_POST["old-email"]) && isset($_POST["new-email"]) && isset($_POST["new-email-repeat"])) {
    $oldEmail = $_POST["old-email"];
    $newEmail = $_POST["new-email"];
    $newEmailRepeat = $_POST["new-email-repeat"];

    if ($newEmail != $newEmailRepeat) {
        $templateParams["error"] = "Le due email inserite non coincidono";
    } else if ($oldEmail != $dbh->getUserFromName($_SESSION["username"])[0]["email"]) {
        $templateParams["error"] = "L'email inserita non corrisponde a quella attuale";
    } else {
        $dbh->changeEmail($oldEmail, $newEmail);
        $_SESSION["email"] = $newEmail;
        header("Location: profile-settings.php?success=1");
        exit;
    }  
}

$templateParams["page"] = "settings-change-email.php";
require_once("template/base.php");
