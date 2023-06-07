<?php
require_once("bootstrap.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

if (isset($_POST["old-password"], $_POST["new-password"], $_POST["new-password-repeat"])) {
    $old_password = $_POST["old-password"];
    $new_password = $_POST["new-password"];
    $confirm_password = $_POST["new-password-repeat"];
    $result = changePassword($old_password, $new_password, $confirm_password, $dbh);
    if ($result[0] == true) {
        // Password changed successfully
        sendEmailAboutPasswordChange(getUserName($dbh), $dbh);
        header("Location: login.php?password_changed=true");
        exit;
    } else {
        // Password couldn't be changed
        $templateParams["errors"] = $result[1];
    }
}

$templateParams["title"]="Cambia password";
$templateParams["page"] = "settings-change-password.php";
require_once("template/base.php");
?>