<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(!empty($_SESSION["error"])) {
    $templateParams["errors"] = array($_SESSION["error"]);
    unset($_SESSION["error"]);
}

$templateParams["user"] = getUserName($dbh);
$templateParams["page"] = "my-profile.php";
require_once("template/base.php");

?>
