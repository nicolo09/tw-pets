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

if(isset($_GET["username"]) && !empty($_GET["username"])) {
    $templateParams["persons"] = $dbh->getPersonsLike($_GET["username"], 0);
    $templateParams["animals"] = $dbh->getAnimalsLike($_GET["username"], 0);
    $templateParams["search"] = $_GET["username"];
}


$templateParams["title"] = "Cerca";
$templateParams["page"] = "search-users.php";
require_once("template/base.php");
?>
