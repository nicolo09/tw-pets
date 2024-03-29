<?php
require_once("bootstrap.php");

if (!login_check($dbh)) {
    header("Location: login.php");
    exit;
}

if(!empty($_SESSION["error"])) {
    $templateParams["errors"] = array($_SESSION["error"]);
    unset($_SESSION["error"]);
}

$templateParams["user"] = getUserName($dbh);
$templateParams["title"] = "Dashboard";
$templateParams["page"] = "my-profile.php";
require_once("template/base.php");

?>
