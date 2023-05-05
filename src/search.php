<?php
require_once("bootstrap.php");

if (!isUserLoggedIn($dbh)) {
    header("Location: login.php");
    exit;
}

if(isset($_GET["username"]) && !empty($_GET["username"])) {
    $templateParams["persons"] = $dbh->getPersonsLike($_GET["username"], 0);
    $templateParams["animals"] = $dbh->getAnimalsLike($_GET["username"], 0);
}

$templateParams["page"] = "search-users.php";
require_once("template/base.php");
?>