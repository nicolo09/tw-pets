<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

$templateParams["page"] = "settings-change-password.php";
require_once("template/base.php");
?>